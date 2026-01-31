<x-app-layout>
    @section('header', $employee->name . ' - Profile')

    <div class="max-w-7xl mx-auto space-y-8">
        
        <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-indigo-100/50 border border-gray-100 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-50 rounded-full -mr-16 -mt-16 opacity-50 blur-3xl"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                
                <div class="flex items-center gap-6">
                    <div class="h-24 w-24 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 p-1 shadow-lg">
                        <div class="h-full w-full rounded-full bg-white flex items-center justify-center text-3xl font-black text-transparent bg-clip-text bg-gradient-to-br from-indigo-600 to-purple-600">
                            {{ substr($employee->name, 0, 1) }}
                        </div>
                    </div>
                    <div>
                        <h1 class="text-3xl font-black text-gray-900 tracking-tight">{{ $employee->name }}</h1>
                        <div class="flex items-center gap-3 mt-2 text-sm font-medium text-gray-500">
                            <span class="px-3 py-1 bg-gray-100 rounded-full text-gray-600 border border-gray-200">{{ $employee->position->name }}</span>
                            <span class="flex items-center gap-1"><i class="fas fa-envelope text-gray-400"></i> {{ $employee->email }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    <div class="bg-gray-50 p-1.5 rounded-xl border border-gray-200 flex">
                        <a href="{{ route('employees.show', ['employee' => $employee->id, 'filter' => 'today']) }}" 
                           class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $filter == 'today' ? 'bg-white text-indigo-600 shadow-sm ring-1 ring-black/5' : 'text-gray-500 hover:text-gray-900' }}">
                           Today
                        </a>
                        <a href="{{ route('employees.show', ['employee' => $employee->id, 'filter' => 'week']) }}" 
                           class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $filter == 'week' ? 'bg-white text-indigo-600 shadow-sm ring-1 ring-black/5' : 'text-gray-500 hover:text-gray-900' }}">
                           Week
                        </a>
                        <a href="{{ route('employees.show', ['employee' => $employee->id, 'filter' => 'month']) }}" 
                           class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $filter == 'month' ? 'bg-white text-indigo-600 shadow-sm ring-1 ring-black/5' : 'text-gray-500 hover:text-gray-900' }}">
                           Month
                        </a>
                    </div>

                    <a href="{{ route('employees.report', ['employee' => $employee->id, 'filter' => $filter]) }}" target="_blank" 
                       class="group px-5 py-3.5 rounded-xl bg-gray-900 text-white font-bold text-xs shadow-lg hover:bg-black transition flex items-center gap-2">
                        <i class="fas fa-file-pdf group-hover:-translate-y-0.5 transition-transform"></i> Export PDF
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-md transition duration-300 group">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-indigo-50 text-indigo-600 rounded-2xl group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300">
                        <i class="fas fa-wallet text-xl"></i>
                    </div>
                    <span class="text-[10px] font-bold text-gray-400 bg-gray-50 px-2 py-1 rounded-full uppercase tracking-wide">{{ ucfirst($filter) }}</span>
                </div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Total Revenue</p>
                <h3 class="text-3xl font-black text-gray-900 mt-1">${{ number_format($totalSales, 2) }}</h3>
            </div>

            <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-md transition duration-300 group">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-blue-50 text-blue-600 rounded-2xl group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                        <i class="fas fa-receipt text-xl"></i>
                    </div>
                </div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Transactions Processed</p>
                <h3 class="text-3xl font-black text-gray-900 mt-1">{{ $totalTransactions }}</h3>
            </div>

            <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-md transition duration-300 group relative overflow-hidden">
                <div class="absolute right-0 top-0 w-24 h-24 bg-emerald-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                <div class="relative z-10">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-emerald-50 text-emerald-600 rounded-2xl group-hover:bg-emerald-500 group-hover:text-white transition-colors duration-300">
                            <i class="fas fa-star text-xl"></i>
                        </div>
                    </div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Performance Status</p>
                    <h3 class="text-3xl font-black text-emerald-500 mt-1">Excellent</h3>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-100/50 border border-gray-100 overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-50 flex items-center gap-3">
                <div class="h-8 w-1 rounded-full bg-indigo-500"></div>
                <h3 class="font-bold text-gray-900 text-lg">Transaction History</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-gray-50/50 text-gray-400 font-bold uppercase text-[10px] tracking-wider border-b border-gray-50">
                            <th class="px-8 py-5">Invoice Details</th>
                            <th class="px-6 py-5">Timestamp</th>
                            <th class="px-6 py-5">Payment Method</th>
                            <th class="px-8 py-5 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($recentSales as $sale)
                        <tr class="group hover:bg-indigo-50/10 transition-colors duration-200">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-xl bg-gray-100 flex items-center justify-center text-gray-400 group-hover:bg-white group-hover:text-indigo-600 group-hover:shadow-sm transition-all border border-transparent group-hover:border-gray-100">
                                        <i class="fas fa-file-invoice"></i>
                                    </div>
                                    <span class="font-mono font-bold text-gray-700 text-sm">{{ $sale->invoice_number }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col">
                                    <span class="font-bold text-gray-800">{{ $sale->created_at->format('M d, Y') }}</span>
                                    <span class="text-xs text-gray-400 font-medium">{{ $sale->created_at->format('h:i A') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                @if($sale->payment_type == 'qr')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-purple-50 text-purple-700 text-xs font-bold border border-purple-100">
                                        <i class="fas fa-qrcode"></i> KHQR
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-100">
                                        <i class="fas fa-money-bill-wave"></i> Cash
                                    </span>
                                @endif
                            </td>
                            <td class="px-8 py-5 text-right">
                                <span class="font-black text-gray-900 text-base group-hover:text-indigo-600 transition-colors">
                                    ${{ number_format($sale->final_total, 2) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-8 py-16 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="h-16 w-16 bg-gray-50 rounded-full flex items-center justify-center mb-3 text-gray-300 text-2xl">
                                        <i class="fas fa-inbox"></i>
                                    </div>
                                    <p class="font-medium">No sales recorded for this period.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>