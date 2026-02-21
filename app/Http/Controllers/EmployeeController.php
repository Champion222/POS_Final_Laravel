<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Sale;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    // 1. LIST EMPLOYEES
    public function index(): View
    {
        $employees = Employee::with('position', 'user')->latest()->get();

        return view('employees.index', compact('employees'));
    }

    // 2. SHOW CREATE FORM
    public function create(): View
    {
        $positions = Position::all();

        return view('employees.create', compact('positions'));
    }

    public function edit(Employee $employee): View
    {
        $positions = Position::all();

        return view('employees.edit', compact('employee', 'positions'));
    }

    // 3. STORE NEW EMPLOYEE
    public function store(StoreEmployeeRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $userId = null;
        $position = Position::findOrFail($validated['position_id']);

        if (in_array($position->target_role, ['admin', 'cashier', 'stock_manager'], true)) {
            $existingUser = User::where('email', $validated['email'])->first();
            if ($existingUser && Employee::where('user_id', $existingUser->id)->exists()) {
                return back()->withInput()->with('error', 'Email is already linked to an employee account.');
            }
        }

        DB::beginTransaction();

        try {
            if (in_array($position->target_role, ['admin', 'cashier', 'stock_manager'], true)) {
                $existingUser = User::where('email', $validated['email'])->first();
                if ($existingUser) {
                    $existingUser->update([
                        'name' => $validated['name'],
                        'role' => $position->target_role,
                    ]);

                    $userId = $existingUser->id;
                } else {
                    $user = User::create([
                        'name' => $validated['name'],
                        'email' => $validated['email'],
                        'password' => Hash::make('genz@123'),
                        'role' => $position->target_role,
                    ]);

                    $userId = $user->id;
                }
            }

            // C. Create Employee Record
            Employee::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'start_date' => $validated['start_date'],
                'position_id' => $validated['position_id'],
                'user_id' => $userId, // Link to user if created, or null
            ]);

            DB::commit(); // Save everything

            return redirect()->route('employees.index')
                ->with('success', $userId
                    ? 'Employee & Login Account created successfully!'
                    : 'Employee created successfully.');

        } catch (\Throwable $e) {
            DB::rollBack(); // Undo if error

            return back()->withInput()->with('error', 'Error creating employee: '.$e->getMessage());
        }
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee): RedirectResponse
    {
        $validated = $request->validated();
        $password = $validated['password'] ?? null;
        $position = Position::findOrFail($validated['position_id']);
        $requiresLogin = in_array($position->target_role, ['admin', 'cashier', 'stock_manager'], true);
        $existingUser = User::where('email', $validated['email'])->first();

        if ($requiresLogin && $existingUser && $existingUser->id !== $employee->user_id) {
            $alreadyLinked = Employee::where('user_id', $existingUser->id)
                ->where('id', '!=', $employee->id)
                ->exists();

            if ($alreadyLinked) {
                return back()->withInput()->with('error', 'Email is already linked to another employee account.');
            }

            if ($employee->user_id !== null) {
                return back()->withInput()->with('error', 'Email is already used by another account.');
            }
        }

        DB::beginTransaction();

        try {
            $userId = $employee->user_id;
            $currentUser = $employee->user ?? ($employee->user_id ? User::find($employee->user_id) : null);

            if ($requiresLogin) {
                if ($currentUser) {
                    $payload = [
                        'name' => $validated['name'],
                        'email' => $validated['email'],
                        'role' => $position->target_role,
                    ];

                    if (! empty($password)) {
                        $payload['password'] = Hash::make($password);
                    }

                    $currentUser->update($payload);
                    $userId = $currentUser->id;
                } elseif ($existingUser) {
                    $payload = [
                        'name' => $validated['name'],
                        'role' => $position->target_role,
                    ];

                    if (! empty($password)) {
                        $payload['password'] = Hash::make($password);
                    }

                    $existingUser->update($payload);
                    $userId = $existingUser->id;
                } else {
                    $passwordToSet = $password ?: 'genz@123';
                    $user = User::create([
                        'name' => $validated['name'],
                        'email' => $validated['email'],
                        'password' => Hash::make($passwordToSet),
                        'role' => $position->target_role,
                    ]);

                    $userId = $user->id;
                }
            } elseif ($employee->user_id) {
                User::destroy($employee->user_id);
                $userId = null;
            }

            $employee->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'start_date' => $validated['start_date'],
                'position_id' => $validated['position_id'],
                'user_id' => $userId,
            ]);

            DB::commit();

            return redirect()->route('employees.show', $employee)
                ->with('success', 'Employee updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Error updating employee: '.$e->getMessage());
        }
    }

    // 4. SHOW PROFILE / STATS (FIXED: Added $filter variable)
    public function show(Request $request, Employee $employee): View
    {
        // FIX: Define $filter from request, defaulting to 'all' or 'today' as needed
        $filter = $request->get('filter', 'all');

        // Simple logic to show sales stats if they are a cashier
        $totalSales = 0;
        $totalTransactions = 0;
        $recentSales = [];

        if ($employee->user_id) {
            $query = Sale::where('user_id', $employee->user_id);

            // Optional: Apply the filter to the query if needed
            if ($filter === 'today') {
                $query->whereDate('created_at', Carbon::today());
            } elseif ($filter === 'month') {
                $query->whereMonth('created_at', Carbon::now()->month);
            }

            $totalSales = $query->sum('final_total');
            $totalTransactions = $query->count();
            $recentSales = $query->latest()->take(5)->get();
        }

        // FIX: Passed 'filter' to the view
        return view('employees.show', compact('employee', 'totalSales', 'totalTransactions', 'recentSales', 'filter'));
    }

    // 5. REPORT EXPORT
    public function report(Request $request, Employee $employee): View
    {
        $filter = $request->get('filter', 'all');

        $totalSales = 0;
        $totalTransactions = 0;
        $recentSales = collect();

        if ($employee->user_id) {
            $query = Sale::where('user_id', $employee->user_id);

            if ($filter === 'today') {
                $query->whereDate('created_at', Carbon::today());
            } elseif ($filter === 'week') {
                $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            } elseif ($filter === 'month') {
                $query->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year);
            }

            $totalSales = $query->sum('final_total');
            $totalTransactions = $query->count();
            $recentSales = $query->latest()->take(10)->get();
        }

        return view('employees.report', compact(
            'employee',
            'filter',
            'totalSales',
            'totalTransactions',
            'recentSales'
        ));
    }

    // 6. DELETE EMPLOYEE
    public function destroy(Employee $employee): RedirectResponse
    {
        if ($employee->user_id) {
            User::destroy($employee->user_id); // Delete linked login
        }
        $employee->delete(); // Delete employee record

        return back()->with('success', 'Employee removed successfully.');
    }
}
