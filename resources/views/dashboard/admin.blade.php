<x-app-layout>
    @section('header', 'Executive Command')

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="max-w-7xl mx-auto space-y-8">
        
        <div class="flex flex-col md:flex-row justify-between items-end md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Overview</h1>
                <div class="flex items-center gap-2 mt-1 text-sm text-gray-500 font-medium">
                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Performance metrics for <span class="text-indigo-600 font-bold">{{ $dateLabel }}</span>
                </div>
            </div>
            
            <div class="bg-white p-1 rounded-xl border border-gray-200 shadow-sm flex">
                <a href="{{ route('dashboard', ['filter' => 'today']) }}" 
                   class="px-5 py-2 rounded-lg text-sm font-bold transition-all {{ $filter == 'today' ? 'bg-gray-900 text-white shadow-md' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">Today</a>
                <a href="{{ route('dashboard', ['filter' => 'week']) }}" 
                   class="px-5 py-2 rounded-lg text-sm font-bold transition-all {{ $filter == 'week' ? 'bg-gray-900 text-white shadow-md' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">Week</a>
                <a href="{{ route('dashboard', ['filter' => 'month']) }}" 
                   class="px-5 py-2 rounded-lg text-sm font-bold transition-all {{ $filter == 'month' ? 'bg-gray-900 text-white shadow-md' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">Month</a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="relative overflow-hidden bg-gradient-to-br from-indigo-600 to-violet-700 rounded-[2rem] p-6 text-white shadow-xl shadow-indigo-200 group hover:scale-[1.02] transition-transform duration-300">
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2 bg-white/20 backdrop-blur-md rounded-lg">
                            <i class="fas fa-wallet text-indigo-100"></i>
                        </div>
                        <p class="text-indigo-100 text-xs font-bold uppercase tracking-wider">Total Revenue</p>
                    </div>
                    <h2 class="text-4xl font-extrabold tracking-tight">${{ number_format($totalRevenue, 2) }}</h2>
                </div>
                <div class="absolute -right-6 -bottom-10 opacity-10 transform rotate-12 group-hover:scale-110 transition duration-500">
                    <i class="fas fa-dollar-sign text-9xl"></i>
                </div>
                <div class="absolute top-0 right-0 p-3 opacity-30">
                    <i class="fas fa-arrow-up text-white transform rotate-45"></i>
                </div>
            </div>

            <div class="bg-white rounded-[2rem] p-6 border border-gray-100 shadow-sm hover:shadow-md transition duration-300 group">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Transactions</p>
                        <h2 class="text-3xl font-extrabold text-gray-900 group-hover:text-indigo-600 transition-colors">{{ number_format($totalTransactions) }}</h2>
                    </div>
                    <div class="h-12 w-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                        <i class="fas fa-receipt"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-2 text-xs font-medium text-gray-400">
                    <span class="text-emerald-500 bg-emerald-50 px-1.5 py-0.5 rounded">Successfully</span> Processed
                </div>
            </div>

            <div class="bg-white rounded-[2rem] p-6 border border-gray-100 shadow-sm hover:shadow-md transition duration-300 group">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Avg. Ticket</p>
                        <h2 class="text-3xl font-extrabold text-gray-900 group-hover:text-emerald-600 transition-colors">${{ number_format($avgTransaction, 2) }}</h2>
                    </div>
                    <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl group-hover:bg-emerald-600 group-hover:text-white transition-colors duration-300">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-2 text-xs font-medium text-gray-400">
                    <span class="text-blue-500 bg-blue-50 px-1.5 py-0.5 rounded">Per Order</span> Average
                </div>
            </div>

            <div class="bg-white rounded-[2rem] p-6 border border-red-100 shadow-sm hover:shadow-md transition duration-300 group relative overflow-hidden">
                <div class="flex justify-between items-start relative z-10">
                    <div>
                        <p class="text-red-400 text-xs font-bold uppercase tracking-wider mb-1">Stock Alerts</p>
                        <h2 class="text-3xl font-extrabold text-red-600">{{ $totalStockAlerts }}</h2>
                    </div>
                    <div class="h-12 w-12 rounded-2xl bg-red-50 text-red-500 flex items-center justify-center text-xl group-hover:animate-pulse">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-2 text-xs font-medium text-red-400 relative z-10">
                    Items below <span class="font-bold text-red-600">reorder level</span>
                </div>
                <div class="absolute inset-0 bg-red-50/30 transform translate-y-full group-hover:translate-y-0 transition-transform duration-500"></div>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            
            <div class="w-full lg:w-2/3 space-y-8">
                
                <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-100/50 border border-gray-100 p-8">
                    <div class="flex justify-between items-center mb-8">
                        <div>
                            <h3 class="font-bold text-gray-900 text-xl">Revenue Trend</h3>
                            <p class="text-sm text-gray-400 font-medium mt-1">Visualizing sales performance over time</p>
                        </div>
                        <span class="inline-flex items-center gap-2 px-3 py-1 bg-indigo-50 text-indigo-700 text-xs font-bold uppercase rounded-full">
                            <span class="w-2 h-2 rounded-full bg-indigo-500"></span> {{ $dateLabel }}
                        </span>
                    </div>
                    <div class="relative h-72 w-full">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>

                <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-100/50 border border-gray-100 overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center bg-white">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-1 rounded-full bg-indigo-500"></div>
                            <h3 class="font-bold text-gray-900 text-lg">Recent Activity</h3>
                        </div>
                        <a href="{{ route('reports.sales') }}" class="text-xs font-bold text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-lg hover:bg-indigo-100 transition-colors">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50/50 text-gray-400 font-bold uppercase text-[10px] tracking-wider border-b border-gray-50">
                                <tr>
                                    <th class="px-8 py-4">Invoice</th>
                                    <th class="px-6 py-4">Cashier</th>
                                    <th class="px-6 py-4">Amount</th>
                                    <th class="px-6 py-4 text-right">Method</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($recentSales as $sale)
                                <tr class="group hover:bg-indigo-50/30 transition duration-200">
                                    <td class="px-8 py-4">
                                        <span class="font-mono text-xs font-bold text-gray-500 group-hover:text-indigo-600 transition-colors">{{ $sale->invoice_number }}</span>
                                        <div class="text-[10px] text-gray-400 mt-0.5">{{ $sale->created_at->format('h:i A') }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-500 border border-gray-200">
                                                {{ substr($sale->cashier->name, 0, 1) }}
                                            </div>
                                            <span class="font-bold text-gray-700 text-sm">{{ $sale->cashier->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-black text-gray-900 text-base">
                                        ${{ number_format($sale->final_total, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @if($sale->payment_type == 'qr')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[10px] font-bold uppercase bg-purple-50 text-purple-700 border border-purple-100">
                                                <i class="fas fa-qrcode"></i> KHQR
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[10px] font-bold uppercase bg-emerald-50 text-emerald-700 border border-emerald-100">
                                                <i class="fas fa-money-bill-wave"></i> Cash
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-12 text-center text-gray-400">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-inbox text-4xl mb-3 opacity-20"></i>
                                            <p class="text-sm font-medium">No sales recorded yet today.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="w-full lg:w-1/3 space-y-8">
                
                <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-100/50 border border-gray-100 p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 bg-yellow-50 text-yellow-600 rounded-xl">
                            <i class="fas fa-trophy text-lg"></i>
                        </div>
                        <h3 class="font-bold text-gray-900 text-lg">Top Performers</h3>
                    </div>

                    <div class="space-y-6">
                        @foreach($cashierStats as $index => $stat)
                        <div class="group">
                            <div class="flex items-center gap-4 mb-2">
                                <span class="text-xs font-black text-gray-300 w-4">#{{ $index + 1 }}</span>
                                <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-gray-600 font-bold shadow-inner text-sm">
                                    {{ substr($stat->cashier->name, 0, 1) }}
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-center">
                                        <span class="font-bold text-gray-800 text-sm">{{ $stat->cashier->name }}</span>
                                        <span class="font-black text-indigo-600 text-sm">${{ number_format($stat->total_sales, 2) }}</span>
                                    </div>
                                    <p class="text-[10px] text-gray-400 font-medium">{{ $stat->count }} Transactions</p>
                                </div>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-1.5 ml-8 overflow-hidden">
                                <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-1.5 rounded-full transition-all duration-1000 ease-out" 
                                     style="width: {{ ($stat->total_sales / ($totalRevenue > 0 ? $totalRevenue : 1)) * 100 }}%"></div>
                            </div>
                        </div>
                        @endforeach
                        
                        @if($cashierStats->isEmpty())
                            <div class="text-center py-6 text-gray-400 text-sm bg-gray-50 rounded-xl border border-dashed border-gray-200">
                                No sales data available.
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-100/50 border border-gray-100 p-8 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-red-50 rounded-bl-full -mr-4 -mt-4 z-0"></div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2 bg-red-50 text-red-500 rounded-xl">
                                <i class="fas fa-exclamation-triangle text-lg"></i>
                            </div>
                            <h3 class="font-bold text-gray-900 text-lg">Inventory Alerts</h3>
                        </div>

                        <div class="space-y-3">
                            @forelse($lowStockProducts as $product)
                            <a href="{{ route('products.edit', $product->id) }}" class="flex items-center gap-4 p-3 rounded-xl bg-white border border-gray-100 hover:border-red-200 hover:shadow-md hover:shadow-red-50 transition group">
                                <div class="h-10 w-10 rounded-lg bg-gray-50 flex-shrink-0 flex items-center justify-center text-gray-400 border border-gray-200 group-hover:border-red-100">
                                    @if($product->image)
                                        <img src="{{ asset('storage/'.$product->image) }}" class="h-full w-full object-cover rounded-lg">
                                    @else
                                        <i class="fas fa-box text-xs"></i>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-bold text-gray-800 text-sm truncate group-hover:text-red-600 transition-colors">{{ $product->name }}</h4>
                                    <p class="text-[10px] text-red-500 font-bold bg-red-50 w-max px-1.5 py-0.5 rounded mt-0.5">Only {{ $product->qty }} Left</p>
                                </div>
                                <div class="text-gray-300 group-hover:text-red-400 transition">
                                    <i class="fas fa-chevron-right text-xs"></i>
                                </div>
                            </a>
                            @empty
                            <div class="text-center py-8">
                                <div class="h-16 w-16 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-check text-2xl text-emerald-500"></i>
                                </div>
                                <p class="text-gray-800 font-bold text-sm">All Good!</p>
                                <p class="text-gray-400 text-xs">Stock levels are healthy.</p>
                            </div>
                            @endforelse
                        </div>

                        @if($totalStockAlerts > 5)
                            <a href="{{ route('products.index') }}" class="block mt-6 text-center text-xs font-bold text-indigo-600 hover:text-indigo-800 bg-indigo-50 py-3 rounded-xl transition-colors">
                                View {{ $totalStockAlerts - 5 }} More Alerts
                            </a>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('salesChart').getContext('2d');
            
            // Modern Gradient
            let gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(99, 102, 241, 0.5)'); // Indigo-500
            gradient.addColorStop(1, 'rgba(99, 102, 241, 0.0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($chartData['labels']),
                    datasets: [{
                        label: 'Revenue',
                        data: @json($chartData['data']),
                        borderColor: '#6366f1', // Indigo-500
                        backgroundColor: gradient,
                        borderWidth: 3,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#6366f1',
                        pointBorderWidth: 3,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        pointHoverBackgroundColor: '#6366f1',
                        pointHoverBorderColor: '#fff',
                        fill: true,
                        tension: 0.4 // Smooth bezier curve
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1e1b4b',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            padding: 12,
                            cornerRadius: 10,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return '$' + context.parsed.y.toFixed(2);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { borderDash: [5, 5], color: '#f1f5f9' },
                            ticks: { 
                                font: { size: 11, family: "'Plus Jakarta Sans', sans-serif" },
                                color: '#94a3b8',
                                callback: function(value) { return '$' + value; } 
                            },
                            border: { display: false }
                        },
                        x: {
                            grid: { display: false },
                            ticks: {
                                font: { size: 11, family: "'Plus Jakarta Sans', sans-serif" },
                                color: '#94a3b8'
                            },
                            border: { display: false }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>