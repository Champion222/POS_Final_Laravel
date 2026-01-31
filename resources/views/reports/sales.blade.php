<x-app-layout>
    @section('header', auth()->user()->role === 'admin' ? 'Global Sales Report' : 'My Sales History')

    @php($sortParams = request()->only(['sort', 'direction']))
    <div class="space-y-8">
        
        <div class="bg-white p-4 rounded-3xl shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
            
            <div class="flex gap-2 bg-gray-50 p-1 rounded-xl">
                <a href="{{ route('reports.sales', array_merge($sortParams, ['filter' => 'today'])) }}" 
                   class="px-5 py-2 rounded-lg text-sm font-bold transition {{ request('filter') == 'today' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:bg-gray-200' }}">
                   Today
                </a>
                <a href="{{ route('reports.sales', array_merge($sortParams, ['filter' => 'week'])) }}" 
                   class="px-5 py-2 rounded-lg text-sm font-bold transition {{ request('filter') == 'week' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:bg-gray-200' }}">
                   Week
                </a>
                <a href="{{ route('reports.sales', array_merge($sortParams, ['filter' => 'month'])) }}" 
                   class="px-5 py-2 rounded-lg text-sm font-bold transition {{ request('filter') == 'month' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:bg-gray-200' }}">
                   Month
                </a>
            </div>

            <form method="GET" action="{{ route('reports.sales') }}" class="flex flex-wrap gap-2 items-center">
                @if(request('filter'))
                    <input type="hidden" name="filter" value="{{ request('filter') }}">
                @endif
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="rounded-xl border-gray-200 text-sm font-bold text-gray-600 focus:ring-indigo-500">
                <span class="text-gray-400 font-bold">-</span>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="rounded-xl border-gray-200 text-sm font-bold text-gray-600 focus:ring-indigo-500">
                <select name="sort" class="rounded-xl border-gray-200 text-sm font-bold text-gray-600 focus:ring-indigo-500">
                    <option value="created_at" @selected(request('sort', 'created_at') === 'created_at')>Date</option>
                    <option value="final_total" @selected(request('sort') === 'final_total')>Amount</option>
                    <option value="invoice_number" @selected(request('sort') === 'invoice_number')>Invoice</option>
                    <option value="payment_type" @selected(request('sort') === 'payment_type')>Payment</option>
                </select>
                <select name="direction" class="rounded-xl border-gray-200 text-sm font-bold text-gray-600 focus:ring-indigo-500">
                    <option value="desc" @selected(request('direction', 'desc') === 'desc')>Desc</option>
                    <option value="asc" @selected(request('direction') === 'asc')>Asc</option>
                </select>
                <button class="w-10 h-10 rounded-xl bg-gray-900 text-white flex items-center justify-center hover:bg-black transition"><i class="fas fa-search"></i></button>
            </form>

            @if(auth()->user()->role === 'admin')
            <a href="{{ route('reports.export', request()->all()) }}" target="_blank" class="px-6 py-2.5 bg-red-50 text-red-600 font-bold rounded-xl border border-red-100 hover:bg-red-100 transition flex items-center gap-2">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
            @elseif(auth()->user()->role === 'cashier')
            <a href="{{ route('reports.my_export', request()->all()) }}" target="_blank" class="px-6 py-2.5 bg-red-50 text-red-600 font-bold rounded-xl border border-red-100 hover:bg-red-100 transition flex items-center gap-2">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gradient-to-br from-indigo-600 to-blue-700 rounded-3xl p-6 text-white shadow-xl shadow-indigo-200 relative overflow-hidden">
                <p class="text-indigo-100 text-xs font-bold uppercase tracking-wider">Total Revenue</p>
                <h2 class="text-3xl font-extrabold mt-2">${{ number_format($totalRevenue, 2) }}</h2>
                <div class="absolute right-0 bottom-0 opacity-10 transform translate-x-2 translate-y-2"><i class="fas fa-wallet text-8xl"></i></div>
            </div>

            <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm relative">
                <p class="text-gray-400 text-xs font-bold uppercase tracking-wider">Total Transactions</p>
                <h2 class="text-3xl font-extrabold text-gray-800 mt-2">{{ $totalTransactions }}</h2>
                <div class="absolute top-4 right-4 p-2 bg-blue-50 text-blue-600 rounded-xl"><i class="fas fa-receipt"></i></div>
            </div>

            <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm relative">
                <p class="text-gray-400 text-xs font-bold uppercase tracking-wider">Avg. Order Value</p>
                <h2 class="text-3xl font-extrabold text-emerald-600 mt-2">${{ number_format($avgOrderValue, 2) }}</h2>
                <div class="absolute top-4 right-4 p-2 bg-emerald-50 text-emerald-600 rounded-xl"><i class="fas fa-chart-line"></i></div>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/30">
                <h3 class="font-bold text-gray-800 text-lg">Sales Log</h3>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">{{ $dateRange }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-white text-gray-400 font-bold uppercase text-xs border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4">Invoice</th>
                            <th class="px-6 py-4">Date</th>
                            @if(auth()->user()->role === 'admin') <th class="px-6 py-4">Cashier</th> @endif
                            <th class="px-6 py-4">Method</th>
                            <th class="px-6 py-4 text-right">Amount</th>
                            <th class="px-6 py-4 text-right">Receipt</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($sales as $sale)
                        <tr class="hover:bg-indigo-50/30 transition">
                            <td class="px-6 py-4 font-mono text-gray-600">{{ $sale->invoice_number }}</td>
                            <td class="px-6 py-4">{{ $sale->created_at->format('M d, h:i A') }}</td>
                            @if(auth()->user()->role === 'admin')
                                <td class="px-6 py-4 font-bold text-gray-700">{{ $sale->cashier->name ?? 'Unknown' }}</td>
                            @endif
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded text-[10px] font-bold uppercase {{ $sale->payment_type == 'cash' ? 'bg-green-50 text-green-700' : 'bg-blue-50 text-blue-700' }}">
                                    {{ $sale->payment_type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-gray-900">${{ number_format($sale->final_total, 2) }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('sales.receipt', $sale) }}" target="_blank" class="inline-flex items-center gap-2 rounded-lg bg-indigo-50 px-3 py-2 text-xs font-bold text-indigo-600 transition hover:bg-indigo-100">
                                    <i class="fas fa-receipt"></i>
                                    View
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 bg-gray-50 border-t border-gray-100">
                {{ $sales->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
