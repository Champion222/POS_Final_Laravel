<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    // 1. MAIN PAGE (Smart View)
    public function index()
    {
        $user = Auth::user();

    // --- ADMIN VIEW LOGIC ---
    if ($user->role === 'admin') {
        $today = Carbon::today();
        $totalEmployees = User::whereIn('role', ['cashier', 'stock_manager', 'employee'])->count();
        
        $presentCount = Attendance::whereDate('date', $today)->distinct('user_id')->count();
        $lateCount = Attendance::whereDate('date', $today)
                    ->whereNotNull('checkin_time')
                    ->whereTime('checkin_time', '>', '09:00:00')
                    ->count();
        
        $stats = [
            'employees' => $totalEmployees,
            'present'   => $presentCount,
            'late'      => $lateCount,
            'absent'    => max(0, $totalEmployees - $presentCount),
        ];

        // FIX: Added 's' to match your folder name "attendances"
        $attendances = Attendance::with('user')->latest()->paginate(10);
        $employees = User::where('role', '!=', 'admin')->get(); 

        return view('attendances.index', compact('stats', 'attendances', 'employees'));
    }

    // --- EMPLOYEE VIEW LOGIC ---
    $myHistory = Attendance::where('user_id', $user->id)->latest()->take(10)->get();
    
    // FIX: Added 's' here too
    return view('attendances.index', compact('myHistory'));
    }

    // 2. CHECK IN / CHECK OUT ACTION
    public function store(Request $request)
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
            if (!$user->isCheckedIn) {
                Attendance::create([
                    'user_id' => $user->id,
                    'checkin_time' => now(),
                    'date' => today(),
                    'status' => now()->format('H:i') > '09:00' ? 'late' : 'present',
                ]);
                return back()->with('success', 'Welcome! You are now clocked in. 🚀');
            }
        } elseif ($type === 'clock_out') {
            $attendance = Attendance::where('user_id', $user->id)
                            ->whereNull('checkout_time')
                            ->latest()
                            ->first();
            
            if ($attendance) {
                $attendance->update(['checkout_time' => now()]);
                return back()->with('success', 'Shift ended successfully! See you tomorrow. 👋');
            }
        }

        return back()->with('error', 'Action failed. Please try again.');
    }

    // 3. ADMIN CHECKOUT (Force checkout)
    public function checkout(Attendance $attendance)
    {
        $attendance->update(['checkout_time' => now()]);
        return back()->with('success', 'Staff member clocked out manually.');
    }

    // 4. EXPORT PDF
    public function exportPdf()
    {
        $attendances = Attendance::with('user')->latest()->get();
        $pdf = Pdf::loadView('attendance.pdf', compact('attendances'));
        return $pdf->download('attendance-report.pdf');
    }
}
