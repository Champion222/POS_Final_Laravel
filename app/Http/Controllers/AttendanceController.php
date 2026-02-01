<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class AttendanceController extends Controller
{
    // 1. MAIN PAGE (Smart View)
    public function index(Request $request): View
    {
        $user = Auth::user();

        // --- ADMIN VIEW LOGIC ---
        if ($user->role === 'admin') {
            $range = $this->resolveDateRange($request->query('range'));
            $sort = $this->resolveSort($request->query('sort'));
            $totalEmployees = User::whereIn('role', ['cashier', 'stock_manager', 'employee'])->count();

            $baseQuery = Attendance::query()
                ->whereDate('date', '>=', $range['start']->toDateString())
                ->whereDate('date', '<=', $range['end']->toDateString());

            $presentCount = (clone $baseQuery)
                ->whereIn('status', ['present', 'late'])
                ->distinct('user_id')
                ->count('user_id');
            $lateCount = (clone $baseQuery)
                ->where('status', 'late')
                ->distinct('user_id')
                ->count('user_id');

            $stats = [
                'employees' => $totalEmployees,
                'present' => $presentCount,
                'late' => $lateCount,
                'absent' => max(0, $totalEmployees - $presentCount),
            ];

            // FIX: Added 's' to match your folder name "attendances"
            $attendancesQuery = Attendance::with('user')
                ->whereDate('date', '>=', $range['start']->toDateString())
                ->whereDate('date', '<=', $range['end']->toDateString());

            if ($sort === 'earliest') {
                $attendancesQuery->orderBy('date')->orderBy('checkin_time');
            } else {
                $attendancesQuery->orderByDesc('date')->orderByDesc('checkin_time');
            }

            $attendances = $attendancesQuery
                ->paginate(10)
                ->appends([
                    'range' => $range['key'],
                    'sort' => $sort,
                ]);
            $employees = User::where('role', '!=', 'admin')->get();

            return view('attendances.index', [
                'stats' => $stats,
                'attendances' => $attendances,
                'employees' => $employees,
                'range' => $range['key'],
                'rangeLabel' => $range['label'],
                'rangeDescription' => $range['description'],
                'sort' => $sort,
            ]);
        }

        // --- EMPLOYEE VIEW LOGIC ---
        $myHistory = Attendance::where('user_id', $user->id)->latest()->take(10)->get();

        // FIX: Added 's' here too
        return view('attendances.index', compact('myHistory'));
    }

    // 2. CHECK IN / CHECK OUT ACTION
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Admin manually adding for someone else
        if ($request->has('employee_id') && $user->role === 'admin') {
            $targetUserId = $request->employee_id;
            Attendance::create([
                'user_id' => $targetUserId,
                'checkin_time' => Carbon::parse($request->check_in),
                'status' => $request->status,
                'date' => today(),
            ]);

            return back()->with('success', 'Manual attendance recorded successfully.');
        }

        // Standard Clock In/Out
        $type = $request->input('type');

        if ($type === 'clock_in') {
            if (! $user->isCheckedIn) {
                Attendance::create([
                    'user_id' => $user->id,
                    'checkin_time' => now(),
                    'date' => today(),
                    'status' => now()->format('H:i') > '09:00' ? 'late' : 'present',
                ]);

                return back()
                    ->with('success', 'Welcome! You are now clocked in. 🚀')
                    ->with('play_sound_text', 'Welcome! You are now clocked in. Thank you.');
            }

            $message = 'You already checked in, '.$user->name.'. Thank you.';

            return back()
                ->with('error', $message)
                ->with('play_sound_text', $message);
        } elseif ($type === 'clock_out') {
            $attendance = Attendance::where('user_id', $user->id)
                ->whereNull('checkout_time')
                ->latest()
                ->first();

            if ($attendance) {
                $attendance->update(['checkout_time' => now()]);

                return back()
                    ->with('success', 'Shift ended successfully! See you tomorrow. 👋')
                    ->with('play_sound_text', 'Shift ended successfully. Thank you.');
            }
        }

        return back()->with('error', 'Action failed. Please try again.');
    }

    // 3. ADMIN CHECKOUT (Force checkout)
    public function checkout(Attendance $attendance): RedirectResponse
    {
        $attendance->update(['checkout_time' => now()]);

        return back()->with('success', 'Staff member clocked out manually.');
    }

    // 4. EXPORT PDF
    public function exportPdf(Request $request): Response
    {
        $range = $this->resolveDateRange($request->query('range'));

        $attendances = Attendance::with('user')
            ->whereDate('date', '>=', $range['start']->toDateString())
            ->whereDate('date', '<=', $range['end']->toDateString())
            ->orderBy('date')
            ->orderBy('checkin_time')
            ->get();

        $totalEmployees = User::whereIn('role', ['cashier', 'stock_manager', 'employee'])->count();

        $presentCount = Attendance::query()
            ->whereDate('date', '>=', $range['start']->toDateString())
            ->whereDate('date', '<=', $range['end']->toDateString())
            ->whereIn('status', ['present', 'late'])
            ->distinct('user_id')
            ->count('user_id');
        $lateCount = Attendance::query()
            ->whereDate('date', '>=', $range['start']->toDateString())
            ->whereDate('date', '<=', $range['end']->toDateString())
            ->where('status', 'late')
            ->distinct('user_id')
            ->count('user_id');

        $stats = [
            'employees' => $totalEmployees,
            'present' => $presentCount,
            'late' => $lateCount,
            'absent' => max(0, $totalEmployees - $presentCount),
        ];

        $pdf = Pdf::loadView('attendances.pdf', [
            'attendances' => $attendances,
            'stats' => $stats,
            'rangeLabel' => $range['label'],
            'rangeDescription' => $range['description'],
        ]);

        return $pdf->download('attendance-report-'.$range['key'].'.pdf');
    }

    /**
     * @return array{key:string, label:string, description:string, start:Carbon, end:Carbon}
     */
    private function resolveDateRange(?string $range): array
    {
        $rangeKey = in_array($range, ['today', 'week', 'month'], true) ? $range : 'today';
        $now = Carbon::now();

        return match ($rangeKey) {
            'week' => [
                'key' => 'week',
                'label' => 'This Week',
                'description' => $now->copy()->startOfWeek()->format('M d, Y').' - '.$now->copy()->endOfWeek()->format('M d, Y'),
                'start' => $now->copy()->startOfWeek(),
                'end' => $now->copy()->endOfWeek(),
            ],
            'month' => [
                'key' => 'month',
                'label' => 'This Month',
                'description' => $now->copy()->startOfMonth()->format('M d, Y').' - '.$now->copy()->endOfMonth()->format('M d, Y'),
                'start' => $now->copy()->startOfMonth(),
                'end' => $now->copy()->endOfMonth(),
            ],
            default => [
                'key' => 'today',
                'label' => 'Today',
                'description' => $now->copy()->format('M d, Y'),
                'start' => $now->copy()->startOfDay(),
                'end' => $now->copy()->endOfDay(),
            ],
        };
    }

    private function resolveSort(?string $sort): string
    {
        return in_array($sort, ['latest', 'earliest'], true) ? $sort : 'latest';
    }
}
