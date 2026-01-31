<x-app-layout>
    @section('header', 'Cashier Workspace')

    <div class="max-w-6xl mx-auto space-y-6">

        <div class="bg-white p-4 rounded-[1.5rem] border border-gray-100 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4 hover:shadow-md transition-all">
            
            <div class="flex items-center gap-4 w-full md:w-auto">
                <div class="relative">
                    <img src="{{ auth()->user()->image ? asset('storage/'.auth()->user()->image) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=6366f1&color=fff' }}" 
                         class="w-12 h-12 rounded-full border-2 border-white shadow-sm object-cover" alt="Avatar">
                    <span class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 border-2 border-white rounded-full"></span>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-gray-900 leading-tight">Hi, {{ auth()->user()->name }}</h1>
                    <p class="text-xs text-gray-500 font-medium">Ready to serve customers?</p>
                </div>
            </div>

            <div class="flex items-center gap-3 w-full md:w-auto bg-gray-50 p-1.5 pr-3 rounded-xl border border-gray-100">
                <form action="{{ route('attendance.store') }}" method="POST" class="w-full">
                    @csrf
                    @if(auth()->user()->isCheckedIn ?? false) 
                        <input type="hidden" name="type" value="clock_out">
                        <button type="submit" class="flex items-center justify-between gap-3 w-full px-4 py-2 bg-white border border-red-100 text-red-600 rounded-lg hover:bg-red-50 hover:border-red-200 transition-all shadow-sm group">
                            <span class="text-xs font-bold uppercase tracking-wider">Clock Out</span>
                            <i class="fas fa-power-off text-xs group-hover:scale-110 transition-transform"></i>
                        </button>
                    @else
                        <input type="hidden" name="type" value="clock_in">
                        <button type="submit" class="flex items-center justify-between gap-3 w-full px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-black transition-all shadow-md group">
                            <span class="text-xs font-bold uppercase tracking-wider">Clock In</span>
                            <i class="fas fa-play text-xs group-hover:scale-110 transition-transform"></i>
                        </button>
                    @endif
                </form>
                
                <div class="h-8 w-px bg-gray-200 mx-1"></div>
                
                <div class="text-right">
                    <p class="text-[10px] font-bold text-gray-400 uppercase">Status</p>
                    <p class="text-xs font-bold {{ (auth()->user()->isCheckedIn ?? false) ? 'text-emerald-600' : 'text-gray-500' }}">
                        {{ (auth()->user()->isCheckedIn ?? false) ? 'Active' : 'Inactive' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="lg:col-span-2 space-y-6">
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between h-28 relative overflow-hidden group hover:border-indigo-100 transition-all">
                        <div class="absolute right-0 top-0 p-3 opacity-10 group-hover:opacity-20 transition-opacity">
                            <i class="fas fa-wallet text-4xl text-indigo-600"></i>
                        </div>
                        <p class="text-xs font-bold text-gray-400 uppercase">Today's Revenue</p>
                        <h3 class="text-2xl font-black text-gray-900 tracking-tight">${{ number_format($todayRevenue ?? 0, 2) }}</h3>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between h-28 relative overflow-hidden group hover:border-blue-100 transition-all">
                        <div class="absolute right-0 top-0 p-3 opacity-10 group-hover:opacity-20 transition-opacity">
                            <i class="fas fa-receipt text-4xl text-blue-600"></i>
                        </div>
                        <p class="text-xs font-bold text-gray-400 uppercase">Transactions</p>
                        <h3 class="text-2xl font-black text-gray-900 tracking-tight">{{ $todaySalesCount ?? 0 }}</h3>
                    </div>
                </div>

                <a href="{{ route('pos.index') }}" class="group relative block w-full bg-gray-900 rounded-[2rem] p-8 overflow-hidden shadow-2xl shadow-gray-900/20 hover:scale-[1.01] transition-transform duration-300">
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 to-purple-700 opacity-90 group-hover:opacity-100 transition-opacity"></div>
                    <div class="absolute -right-10 -bottom-10 text-9xl text-white opacity-10 transform rotate-12 group-hover:rotate-0 group-hover:scale-110 transition-all duration-500">
                        <i class="fas fa-cash-register"></i>
                    </div>
                    
                    <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
                        <div>
                            <span class="inline-flex items-center gap-2 px-3 py-1 bg-white/10 backdrop-blur rounded-full border border-white/20 text-[10px] font-bold text-white uppercase tracking-wider mb-3">
                                <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span> System Ready
                            </span>
                            <h2 class="text-3xl font-black text-white leading-none mb-2">Open Terminal</h2>
                            <p class="text-indigo-100 text-sm max-w-xs">Start processing sales and managing orders.</p>
                        </div>
                        
                        <div class="h-12 w-12 bg-white rounded-full flex items-center justify-center text-indigo-600 shadow-lg group-hover:scale-110 transition-transform">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>
                </a>

            </div>

            <div class="lg:col-span-1">
                <div class="bg-white border border-gray-100 rounded-[2rem] p-6 shadow-sm h-full flex flex-col">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-gray-900 text-sm uppercase tracking-wide">Recent Sales</h3>
                        <a href="{{ route('reports.sales') }}" class="text-[10px] font-bold bg-gray-50 px-2 py-1 rounded text-gray-500 hover:bg-gray-100 hover:text-indigo-600 transition">See All</a>
                    </div>

                    <div class="flex-1 overflow-y-auto custom-scrollbar pr-2 space-y-3 max-h-[300px] lg:max-h-none">
                        @forelse(\App\Models\Sale::where('user_id', auth()->id())->latest()->take(5)->get() as $sale)
                        <div class="group flex items-center justify-between p-3 bg-gray-50 rounded-xl hover:bg-indigo-50 border border-transparent hover:border-indigo-100 transition-all cursor-default">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-gray-400 group-hover:text-indigo-600 transition-colors shadow-sm">
                                    <i class="fas fa-shopping-bag text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-900 font-mono">{{ $sale->invoice_number }}</p>
                                    <p class="text-[10px] text-gray-400 font-medium">{{ $sale->created_at->format('h:i A') }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-black text-gray-900 group-hover:text-indigo-600 transition-colors">+${{ number_format($sale->final_total, 2) }}</p>
                                <span class="text-[9px] font-bold text-gray-400 uppercase bg-white px-1.5 py-0.5 rounded border border-gray-100">{{ $sale->payment_type }}</span>
                            </div>
                        </div>
                        @empty
                        <div class="flex flex-col items-center justify-center h-40 text-center opacity-50">
                            <i class="fas fa-inbox text-3xl text-gray-300 mb-2"></i>
                            <p class="text-xs font-bold text-gray-400">No sales yet today</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
