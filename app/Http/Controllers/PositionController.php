<?php

namespace App\Http\Controllers;

use App\Models\Position;
use App\Models\Employee;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index()
    {
        // Get positions with staff count
        $positions = Position::withCount('employees')->latest()->get();
        
        // Calculate Stats
        $totalPositions = $positions->count();
        $totalStaff = Employee::count();
        
        // Count how many roles have system login access (Added 'stock_manager')
        $systemRoles = $positions->filter(function ($pos) {
            return in_array($pos->target_role, ['admin', 'cashier', 'stock_manager']);
        })->count();

        return view('positions.index', compact('positions', 'totalPositions', 'totalStaff', 'systemRoles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:positions,name',
            'base_salary' => 'required|numeric',
            'target_role' => 'required'
        ]);

        Position::create($request->all());

        return back()->with('success', 'New position created successfully.');
    }

    public function destroy(Position $position)
    {
        // Load count if not already loaded
        $position->loadCount('employees');
        
        if ($position->employees_count > 0) {
            return back()->with('error', 'Cannot delete: There are staff members assigned to this position.');
        }
        
        $position->delete();
        return back()->with('success', 'Position deleted.');
    }
}