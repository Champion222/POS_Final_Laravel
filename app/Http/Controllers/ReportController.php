<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    /**
     * Helper to build the query based on filters
     */
    private function getFilteredSalesQuery(Request $request): Builder
    {
        $query = Sale::query();
        
        // FIX: Use Auth::user() instead of auth()->user() to satisfy the code editor
        $user = Auth::user(); 

        // 1. Role Check: Cashiers only see their own sales
        if ($user->role === 'cashier') {
            $query->where('user_id', $user->id);
        }

        // 2. Date Filtering
        if ($request->has('start_date') && $request->has('end_date') && $request->start_date && $request->end_date) {
            // Custom Range
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('created_at', [$start, $end]);
        } elseif ($request->filter == 'today') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($request->filter == 'week') {
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($request->filter == 'month') {
            $query->whereMonth('created_at', Carbon::now()->month)
                  ->whereYear('created_at', Carbon::now()->year);
        }

        return $query;
    }

    private function applySort(Builder $query, Request $request): Builder
    {
        $sort = $request->get('sort', 'created_at');
        $direction = strtolower((string) $request->get('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        $allowed = ['created_at', 'final_total', 'invoice_number', 'payment_type'];
        if (!in_array($sort, $allowed, true)) {
            $sort = 'created_at';
        }

        return $query->orderBy($sort, $direction);
    }

    /**
     * Display the Sales Report Page
     */
    public function sales(Request $request): View
    {
        $query = $this->getFilteredSalesQuery($request);
        
        // Clone query for stats to avoid resetting the main query
        $statsQuery = clone $query;

        // Get Data (Paginated for web view)
        $sales = $this->applySort($query, $request)->with('cashier')->paginate(15);
        
        // Calculate Totals
        $totalRevenue = $statsQuery->sum('final_total');
        $totalTransactions = $statsQuery->count();
        $avgOrderValue = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        // Stats for Charts/Cards
        $cashierStats = Sale::select('user_id', DB::raw('sum(final_total) as total_sales'), DB::raw('count(*) as count'))
            ->whereIn('id', $statsQuery->pluck('id')) 
            ->groupBy('user_id')
            ->with('cashier')
            ->orderByDesc('total_sales')
            ->get();

        // Date Label for UI
        $dateRange = $this->getDateLabel($request);

        return view('reports.sales', compact(
            'sales', 'totalRevenue', 'totalTransactions', 'avgOrderValue', 'cashierStats', 'dateRange'
        ));
    }

    /**
     * Export to PDF
     */
    public function exportPdf(Request $request): Response
    {
        $query = $this->getFilteredSalesQuery($request);
        
        // Fetch ALL data for PDF (no pagination)
        $sales = $this->applySort($query, $request)->with('cashier')->get();
        
        $totalRevenue = $sales->sum('final_total');
        $totalTransactions = $sales->count();
        $avgOrderValue = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        $cashierStats = Sale::select('user_id', DB::raw('sum(final_total) as total_sales'), DB::raw('count(*) as count'))
            ->whereIn('id', $sales->pluck('id')) 
            ->groupBy('user_id')
            ->with('cashier')
            ->orderByDesc('total_sales')
            ->get();

        $topCashier = $cashierStats->first();
        $dateRange = $this->getDateLabel($request);

        $pdf = Pdf::loadView('reports.pdf', compact(
            'sales', 'totalRevenue', 'totalTransactions', 'avgOrderValue', 
            'cashierStats', 'topCashier', 'dateRange'
        ));

        // Use landscape for better table view
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('sales_report_' . ($request->filter ?? 'custom') . '_' . now()->format('Ymd') . '.pdf');
    }

    /**
     * Export the current cashier's sales to PDF
     */
    public function exportMySalesPdf(Request $request): Response
    {
        $query = $this->getFilteredSalesQuery($request);

        $sales = $this->applySort($query, $request)->with('cashier')->get();

        $totalRevenue = $sales->sum('final_total');
        $totalTransactions = $sales->count();
        $avgOrderValue = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        $cashierStats = Sale::select('user_id', DB::raw('sum(final_total) as total_sales'), DB::raw('count(*) as count'))
            ->whereIn('id', $sales->pluck('id'))
            ->groupBy('user_id')
            ->with('cashier')
            ->orderByDesc('total_sales')
            ->get();

        $topCashier = $cashierStats->first();
        $dateRange = $this->getDateLabel($request);

        $pdf = Pdf::loadView('reports.pdf', compact(
            'sales', 'totalRevenue', 'totalTransactions', 'avgOrderValue',
            'cashierStats', 'topCashier', 'dateRange'
        ));

        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('my_sales_report_' . ($request->filter ?? 'custom') . '_' . now()->format('Ymd') . '.pdf');
    }

    /**
     * Helper to get a readable date label
     */
    private function getDateLabel(Request $request): string
    {
        if ($request->has('start_date') && $request->end_date) {
            return Carbon::parse($request->start_date)->format('M d') . ' - ' . Carbon::parse($request->end_date)->format('M d');
        } elseif ($request->filter == 'today') {
            return 'Today (' . Carbon::today()->format('M d') . ')';
        } elseif ($request->filter == 'week') {
            return 'This Week';
        } elseif ($request->filter == 'month') {
            return 'This Month (' . Carbon::now()->format('F') . ')';
        }
        return 'All Time';
    }
    
    public function stock(): View
    {
        return view('reports.stock'); 
    }
}
