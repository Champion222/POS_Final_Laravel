<x-app-layout>
    @section('header', 'Stock Management')

    <div class="max-w-7xl mx-auto space-y-8">

        <div class="relative overflow-hidden rounded-[2.5rem] bg-gray-900 p-8 shadow-2xl shadow-gray-900/20">
            <div class="absolute top-0 right-0 -mr-20 -mt-20 h-80 w-80 rounded-full bg-indigo-600 opacity-20 blur-[80px]"></div>
            <div class="absolute bottom-0 left-0 -ml-20 -mb-20 h-80 w-80 rounded-full bg-blue-600 opacity-20 blur-[80px]"></div>

            <div class="relative z-10 flex flex-col lg:flex-row justify-between items-center gap-8">
                
                <div class="flex items-center gap-6 w-full lg:w-auto">
                    <div class="h-20 w-20 rounded-3xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-3xl shadow-lg shadow-indigo-500/30 transform rotate-3">
                        <i class="fas fa-boxes-stacked"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-black text-white tracking-tight">Inventory Control</h1>
                        <p class="text-indigo-200 text-sm mt-1 font-medium">Manage your products, track stock levels, and organize categories.</p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-4 w-full lg:w-auto bg-white/5 p-2 pr-6 rounded-2xl border border-white/10 backdrop-blur-md">
                    
                    <div class="hidden sm:block text-right px-4 border-r border-white/10">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">My Status</p>
                        @if(auth()->user()->isCheckedIn ?? false)
                            <div class="flex items-center justify-end gap-1.5">
                                <span class="relative flex h-2.5 w-2.5">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                                </span>
                                <span class="text-emerald-400 font-bold text-sm">On Duty</span>
                            </div>
                        @else
                            <span class="text-gray-500 font-bold text-sm">Off Duty</span>
                        @endif
                    </div>

                    <form action="{{ route('attendance.store') }}" method="POST" class="w-full sm:w-auto">
                        @csrf
                        @if(auth()->user()->isCheckedIn ?? false)
                            <input type="hidden" name="type" value="clock_out">
                            <button type="submit" class="group flex w-full sm:w-auto items-center justify-center gap-3 px-6 py-3 bg-red-500/10 text-red-400 border border-red-500/20 rounded-xl hover:bg-red-500 hover:text-white transition-all duration-300">
                                <i class="fas fa-power-off text-sm"></i>
                                <span class="text-xs font-bold uppercase tracking-wider">Clock Out</span>
                            </button>
                        @else
                            <input type="hidden" name="type" value="clock_in">
                            <button type="submit" class="group flex w-full sm:w-auto items-center justify-center gap-3 px-6 py-3 bg-indigo-500 text-white rounded-xl hover:bg-indigo-400 transition-all duration-300 shadow-lg shadow-indigo-500/20">
                                <i class="fas fa-play text-xs"></i>
                                <span class="text-xs font-bold uppercase tracking-wider">Start Shift</span>
                            </button>
                        @endif
                    </form>

                    <a href="{{ route('profile.edit') }}" class="hidden sm:flex items-center gap-3 pl-2 group">
                        <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}&background=random" class="w-10 h-10 rounded-full border-2 border-white/20 group-hover:border-indigo-400 transition-colors">
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <div class="relative overflow-hidden bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 group hover:shadow-md transition-all duration-300">
                <div class="absolute right-0 top-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity transform group-hover:scale-110 duration-500">
                    <i class="fas fa-cube text-7xl text-indigo-600"></i>
                </div>
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                            <i class="fas fa-box"></i>
                        </div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Products</p>
                    </div>
                    <h3 class="text-3xl font-black text-gray-900">{{ \App\Models\Product::count() }}</h3>
                    <div class="mt-3 inline-flex items-center px-2 py-1 rounded-md bg-green-50 text-green-700 text-[10px] font-bold uppercase tracking-wide">
                        <i class="fas fa-circle text-[6px] mr-1.5"></i> Active Catalog
                    </div>
                </div>
            </div>

            <div class="relative overflow-hidden bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 group hover:shadow-md transition-all duration-300">
                <div class="absolute right-0 top-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity transform group-hover:scale-110 duration-500">
                    <i class="fas fa-sack-dollar text-7xl text-emerald-600"></i>
                </div>
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Inventory Value</p>
                    </div>
                    <h3 class="text-3xl font-black text-gray-900">${{ number_format(\App\Models\Product::sum(\DB::raw('sale_price * qty')), 2) }}</h3>
                    <p class="mt-3 text-xs text-gray-400 font-medium">Estimated Retail Value</p>
                </div>
            </div>

            <div class="relative overflow-hidden bg-red-50 p-6 rounded-[2rem] border border-red-100 shadow-inner group hover:shadow-md transition-all duration-300">
                <div class="absolute -right-4 -bottom-4 text-red-200 opacity-50 transform rotate-12 group-hover:scale-110 transition-transform">
                    <i class="fas fa-bell text-8xl"></i>
                </div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-bold text-red-400 uppercase tracking-wider">Low Stock</p>
                        <span class="flex h-2 w-2 rounded-full bg-red-500 animate-pulse"></span>
                    </div>
                    <h3 class="text-4xl font-black text-red-600">{{ \App\Models\Product::where('qty', '<=', 10)->count() }}</h3>
                    <p class="mt-2 text-xs font-bold text-red-400 uppercase">Items need attention</p>
                </div>
            </div>

            <a href="{{ route('categories.index') }}" class="relative group block bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm hover:border-indigo-100 hover:shadow-lg hover:shadow-indigo-100 transition-all duration-300">
                <div class="flex flex-col h-full justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-2 bg-gray-50 text-gray-400 rounded-lg group-hover:bg-indigo-50 group-hover:text-indigo-600 transition-colors">
                                <i class="fas fa-tags"></i>
                            </div>
                            <i class="fas fa-arrow-right text-gray-300 group-hover:text-indigo-500 group-hover:translate-x-1 transition-all"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 group-hover:text-indigo-700 transition-colors">Categories</h3>
                        <p class="text-sm text-gray-400 mt-1">Manage {{ \App\Models\Category::count() }} groups</p>
                    </div>
                    <div class="mt-4 w-full bg-gray-100 h-1 rounded-full overflow-hidden">
                        <div class="bg-indigo-500 h-full w-2/3 group-hover:w-full transition-all duration-500"></div>
                    </div>
                </div>
            </a>
        </div>

        <div class="w-full">
            <a href="{{ route('pos.index') }}" class="group relative flex items-center justify-between w-full bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 rounded-3xl p-1 overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-1">
                <div class="absolute inset-0 bg-gradient-to-r from-indigo-500 to-purple-500 opacity-0 group-hover:opacity-10 transition-opacity duration-500"></div>
                
                <div class="flex-1 flex items-center justify-between px-8 py-6 relative z-10">
                    <div class="flex items-center gap-5">
                        <div class="h-14 w-14 rounded-2xl bg-white/10 flex items-center justify-center text-2xl text-indigo-400 border border-white/10 group-hover:bg-white/20 group-hover:text-white transition-colors">
                            <i class="fas fa-cash-register"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white group-hover:text-indigo-100 transition-colors">Open POS Terminal</h2>
                            <p class="text-gray-400 text-sm group-hover:text-gray-300 transition-colors">Process sales directly from here.</p>
                        </div>
                    </div>
                    <div class="h-10 w-10 bg-white rounded-full flex items-center justify-center text-gray-900 shadow-lg transform group-hover:rotate-45 transition-transform duration-500">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2 bg-white rounded-[2.5rem] shadow-xl shadow-gray-100/50 border border-gray-100 overflow-hidden flex flex-col h-full">
                <div class="p-8 border-b border-gray-50 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div>
                        <h3 class="font-bold text-xl text-gray-900">Inventory Items</h3>
                        <p class="text-sm text-gray-400 mt-1">Latest added products</p>
                    </div>
                    <a href="{{ route('products.create') }}" class="group flex items-center gap-2 bg-gray-900 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-lg hover:bg-black hover:scale-105 transition-all">
                        <i class="fas fa-plus text-xs group-hover:rotate-90 transition-transform"></i> 
                        <span>Add Product</span>
                    </a>
                </div>
                
                <div class="overflow-x-auto flex-1">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50/50 text-gray-400 font-bold uppercase text-[10px] tracking-wider border-b border-gray-50">
                            <tr>
                                <th class="px-8 py-4">Product Details</th>
                                <th class="px-6 py-4">In Stock</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse(\App\Models\Product::latest()->take(6)->get() as $product)
                            <tr class="group hover:bg-indigo-50/30 transition duration-200">
                                <td class="px-8 py-4">
                                    <div>
                                        <p class="font-bold text-gray-800 text-base group-hover:text-indigo-600 transition-colors">{{ $product->name }}</p>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide mt-0.5">{{ $product->category->name ?? 'Uncategorized' }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-mono font-bold text-gray-700 text-sm">{{ $product->qty }}</span> 
                                    <span class="text-xs text-gray-400">units</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($product->qty <= 5) 
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-bold uppercase bg-red-50 text-red-600 border border-red-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span> Critical
                                        </span>
                                    @elseif($product->qty <= 20) 
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-bold uppercase bg-orange-50 text-orange-600 border border-orange-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span> Low
                                        </span>
                                    @else 
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-bold uppercase bg-emerald-50 text-emerald-600 border border-emerald-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Good
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('products.edit', $product->id) }}" class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-white border border-gray-200 text-gray-400 hover:text-indigo-600 hover:border-indigo-200 transition shadow-sm">
                                        <i class="fas fa-pen text-xs"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="p-8 text-center text-gray-400">No items found in inventory.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-100/50 border border-gray-100 p-8 flex flex-col h-full relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-red-50 rounded-bl-full -mr-10 -mt-10 z-0 opacity-50"></div>

                <div class="relative z-10 flex items-center gap-3 mb-8">
                    <div class="h-10 w-10 rounded-xl bg-red-50 text-red-500 flex items-center justify-center border border-red-100">
                        <i class="fas fa-bell animate-swing"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 text-lg">Critical Stock</h3>
                </div>

                <div class="space-y-4 relative z-10 flex-1 overflow-y-auto custom-scrollbar pr-2 max-h-[400px]">
                    @forelse(\App\Models\Product::where('qty', '<=', 10)->take(5)->get() as $alertProd)
                    <div class="flex items-start gap-4 p-4 rounded-2xl bg-white border border-red-100 shadow-sm hover:shadow-md hover:border-red-200 transition-all group">
                        <div class="h-12 w-12 rounded-xl bg-red-50 flex items-center justify-center text-red-400 shrink-0 group-hover:bg-red-100 transition-colors">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-gray-900 text-sm truncate">{{ $alertProd->name }}</h4>
                            <p class="text-xs text-red-500 font-bold mt-0.5">{{ $alertProd->qty }} Units Left</p>
                            <div class="w-full bg-red-100 h-1 rounded-full mt-2 overflow-hidden">
                                <div class="bg-red-500 h-full rounded-full" style="width: {{ ($alertProd->qty / 10) * 100 }}%"></div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="flex flex-col items-center justify-center h-40 text-center">
                        <div class="h-16 w-16 bg-emerald-50 rounded-full flex items-center justify-center text-emerald-500 text-2xl mb-3">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <p class="text-sm font-bold text-gray-800">Everything looks good!</p>
                        <p class="text-xs text-gray-400 mt-1">Stock levels are healthy.</p>
                    </div>
                    @endforelse
                </div>

                @if(\App\Models\Product::where('qty', '<=', 10)->count() > 5)
                    <div class="mt-6 pt-4 border-t border-gray-50 relative z-10">
                        <a href="{{ route('products.index') }}" class="flex w-full items-center justify-center gap-2 rounded-xl bg-red-50 px-4 py-3 text-xs font-bold text-red-600 hover:bg-red-100 transition">
                            View All Alerts <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                @endif
            </div>

        </div>

    </div>
</x-app-layout>