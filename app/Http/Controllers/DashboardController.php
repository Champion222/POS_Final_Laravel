<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // =========================================================
        // 1. CASHIER DASHBOARD
        // =========================================================
        if ($user->role === 'cashier') {
            $todayRevenue = Sale::where('user_id', $user->id)
                ->whereDate('created_at', Carbon::today())
                ->sum('final_total');

            $todaySalesCount = Sale::where('user_id', $user->id)
                ->whereDate('created_at', Carbon::today())
                ->count();

            // Prevent division by zero
            $avgTicket = $todaySalesCount > 0 ? $todayRevenue / $todaySalesCount : 0;

            return view('dashboard.cashier', compact('todayRevenue', 'todaySalesCount', 'avgTicket'));
        }

        // =========================================================
        // 2. STOCK MANAGER DASHBOARD
        // =========================================================
        if ($user->role === 'stock_manager') {
            
            // 1. Total Products Count
            $totalProducts = Product::count();

            // 2. Total Stock Value
            // FIX: Changed 'price' to 'sale_price' based on your database screenshot
            // Note: If you want to see the "Cost" value instead, change 'sale_price' to 'cost_price'
            $stockValue = Product::sum(DB::raw('sale_price * qty')); 

            // 3. Low Stock Alerts
            $lowStockCount = Product::where('qty', '<=', 10)->count();

            // 4. Categories Count
            $categoriesCount = Category::count();

            return view('dashboard.stock', compact('totalProducts', 'stockValue', 'lowStockCount', 'categoriesCount'));
        }

        // =========================================================
        // 3. ADMIN DASHBOARD (Default / Full Access)
        // =========================================================
        if ($user->role === 'admin') {
            
            // --- Date Filter Logic ---
            $filter = $request->get('filter', 'today'); // Default to today
            $query = Sale::query();
            $dateLabel = '';

            if ($filter == 'today') {
                $query->whereDate('created_at', Carbon::today());
                $dateLabel = 'Today';
            } elseif ($filter == 'week') {
                $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                $dateLabel = 'This Week';
            } elseif ($filter == 'month') {
                $query->whereMonth('created_at', Carbon::now()->month);
                $dateLabel = 'This Month';
            }

            // --- KPI CARDS ---
            $statsQuery = clone $query; 
            
            $totalRevenue = $statsQuery->sum('final_total');
            $totalTransactions = $statsQuery->count();
            $avgTransaction = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;
            
            // --- CHART DATA (Sales over time) ---
            $chartData = $this->getChartData($filter);

            // --- CASHIER LEADERBOARD ---
            $cashierStats = Sale::select('user_id', DB::raw('sum(final_total) as total_sales'), DB::raw('count(*) as count'))
                ->whereIn('id', $query->pluck('id')) 
                ->groupBy('user_id')
                ->with('cashier') 
                ->orderByDesc('total_sales')
                ->take(5)
                ->get();

            // --- OTHER DATA ---
            $recentSales = Sale::with('cashier')->latest()->take(6)->get();
            
            // FIX: Ensure 'qty' is used here as well
            $lowStockProducts = Product::where('qty', '<', 10)->take(5)->get();
            $totalStockAlerts = Product::where('qty', '<', 10)->count();

            return view('dashboard.admin', compact(
                'totalRevenue', 'totalTransactions', 'avgTransaction', 
                'recentSales', 'lowStockProducts', 'totalStockAlerts', 
                'cashierStats', 'chartData', 'filter', 'dateLabel'
            ));
        }

        return abort(403, 'Unauthorized action.');
    }

    /**
     * Helper function to generate Chart.js data
     */
    private function getChartData($filter)
    {
        $data = [];
        $labels = [];
        
        if ($filter == 'today') {
            // Group by Hour (00 - 23)
            $sales = Sale::whereDate('created_at', Carbon::today())
                ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('SUM(final_total) as total'))
                ->groupBy('hour')
                ->pluck('total', 'hour');

            for ($i = 8; $i <= 22; $i++) { 
                $labels[] = date("g A", mktime($i, 0));
                $data[] = $sales[$i] ?? 0;
            }
        } else {
            // Group by Day
            $startDate = ($filter == 'week') ? Carbon::now()->startOfWeek() : Carbon::now()->startOfMonth();
            $endDate = ($filter == 'week') ? Carbon::now()->endOfWeek() : Carbon::now()->endOfMonth();
            
            $sales = Sale::whereBetween('created_at', [$startDate, $endDate])
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(final_total) as total'))
                ->groupBy('date')
                ->pluck('total', 'date');

            $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
            foreach ($period as $date) {
                $labels[] = $date->format('M d');
                $data[] = $sales[$date->format('Y-m-d')] ?? 0;
            }
        }

        return ['labels' => $labels, 'data' => $data];
    }
}