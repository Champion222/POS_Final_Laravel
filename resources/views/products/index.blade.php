<x-app-layout>
    @section('header', 'Inventory Management')

    <div class="max-w-7xl mx-auto space-y-8">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-[2rem] p-6 border border-gray-100 shadow-xl shadow-gray-100/50 flex items-center gap-5 transition-transform hover:scale-[1.02] duration-300">
                <div class="h-16 w-16 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 text-3xl shadow-sm">
                    <i class="fas fa-boxes-stacked"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Total SKU Count</p>
                    <h3 class="text-3xl font-black text-gray-900">{{ $totalProducts }} <span class="text-lg font-medium text-gray-400">Items</span></h3>
                </div>
            </div>

            <div class="relative overflow-hidden bg-gradient-to-br from-emerald-500 to-teal-600 rounded-[2rem] p-6 text-white shadow-2xl shadow-emerald-200 group">
                <div class="relative z-10">
                    <p class="text-emerald-100 font-bold text-xs uppercase tracking-widest mb-1">Total Inventory Value</p>
                    <h2 class="text-4xl font-black tracking-tight mt-1">${{ number_format($totalValue, 2) }}</h2>
                    <div class="mt-3 flex items-center gap-2 text-xs font-medium text-emerald-100 bg-white/10 w-max px-3 py-1 rounded-full backdrop-blur-md">
                        <i class="fas fa-arrow-trend-up"></i> Potential Revenue
                    </div>
                </div>
                <div class="absolute right-0 bottom-0 opacity-10 transform translate-x-4 translate-y-4 group-hover:scale-110 transition duration-500">
                    <i class="fas fa-sack-dollar text-9xl"></i>
                </div>
                <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
            </div>

            <div class="bg-white rounded-[2rem] p-6 border border-red-100 shadow-xl shadow-red-50/50 flex items-center gap-5 relative overflow-hidden group">
                <div class="h-16 w-16 rounded-2xl bg-red-50 flex items-center justify-center text-red-500 text-3xl shadow-sm group-hover:animate-pulse">
                    <i class="fas fa-bell"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-red-400 uppercase tracking-widest mb-1">Needs Attention</p>
                    <h3 class="text-3xl font-black text-red-600">{{ $lowStockCount }} <span class="text-lg font-medium text-red-400">Low Stock</span></h3>
                </div>
                <div class="absolute right-0 top-0 w-16 h-16 bg-red-50 rounded-bl-full -mr-8 -mt-8 opacity-50"></div>
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-100/50 border border-gray-100 overflow-hidden">
            
            <div class="px-8 py-6 border-b border-gray-50 flex flex-col md:flex-row justify-between items-center gap-4 bg-white">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-1 rounded-full bg-indigo-500"></div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-xl">Product List</h3>
                        <p class="text-sm text-gray-400 font-medium">Manage your catalog and stock levels.</p>
                    </div>
                </div>
                <a href="{{ route('products.create') }}" class="group bg-gray-900 hover:bg-black text-white px-6 py-3.5 rounded-2xl font-bold shadow-lg shadow-gray-300 transform hover:scale-[1.02] active:scale-95 transition-all flex items-center gap-3">
                    <div class="bg-white/20 p-1 rounded-lg"><i class="fas fa-plus text-xs"></i></div>
                    <span>Add New Product</span>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50/50 text-gray-400 font-bold uppercase text-[10px] tracking-wider border-b border-gray-50">
                        <tr>
                            <th class="px-8 py-5">Product Details</th>
                            <th class="px-6 py-5">Category & Code</th>
                            <th class="px-6 py-5">Stock Status</th>
                            <th class="px-8 py-5 text-right">Price & Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($products as $product)
                        <tr class="group hover:bg-indigo-50/10 transition-colors duration-200">
                            
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-5">
                                    <div class="h-16 w-16 rounded-2xl bg-gray-50 border border-gray-100 overflow-hidden shrink-0 shadow-sm group-hover:shadow-md transition-all">
                                        @if($product->image)
                                            <img src="{{ asset('storage/'.$product->image) }}" class="h-full w-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                                        @else
                                            <div class="h-full w-full flex items-center justify-center text-gray-300 bg-gray-50">
                                                <i class="fas fa-image text-2xl opacity-50"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900 text-base mb-1 group-hover:text-indigo-600 transition-colors">{{ $product->name }}</h4>
                                        <p class="text-xs text-gray-400 font-medium">Added: {{ $product->created_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                            </td>
                            
                            <td class="px-6 py-5">
                                <div class="flex flex-col gap-2">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wide bg-indigo-50 text-indigo-600 border border-indigo-100 w-max">
                                        {{ $product->category->name ?? 'Uncategorized' }}
                                    </span>
                                    <div class="flex items-center gap-2 text-gray-500 font-mono text-xs bg-gray-50 px-2 py-1 rounded-md w-max border border-gray-100">
                                        <i class="fas fa-barcode text-gray-300"></i> {{ $product->barcode }}
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-5">
                                @if($product->qty <= 0)
                                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-extrabold bg-red-50 text-red-600 border border-red-100">
                                        <span class="w-2 h-2 rounded-full bg-red-500"></span> Out of Stock (0)
                                    </span>
                                @elseif($product->qty < 10)
                                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-extrabold bg-orange-50 text-orange-600 border border-orange-100">
                                        <span class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span> Low Stock ({{ $product->qty }})
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-extrabold bg-emerald-50 text-emerald-600 border border-emerald-100">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> In Stock ({{ $product->qty }})
                                    </span>
                                @endif
                            </td>

                            <td class="px-8 py-5 text-right">
                                <div class="flex flex-col items-end gap-3">
                                    <p class="text-lg font-black text-gray-900 tracking-tight">${{ number_format($product->sale_price, 2) }}</p>
                                    
                                    <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-all duration-200 transform translate-x-2 group-hover:translate-x-0">
                                        <a href="{{ route('products.edit', $product->id) }}" class="h-9 w-9 flex items-center justify-center rounded-xl bg-white border border-gray-200 text-gray-400 hover:text-indigo-600 hover:border-indigo-200 hover:shadow-sm transition-all" title="Edit">
                                            <i class="fas fa-pen text-xs"></i>
                                        </a>
                                        
                                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Delete this product?');">
                                            @csrf @method('DELETE')
                                            <button class="h-9 w-9 flex items-center justify-center rounded-xl bg-white border border-gray-200 text-gray-400 hover:text-red-600 hover:border-red-200 hover:bg-red-50 transition-all" title="Delete">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-8 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="h-20 w-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-box-open text-4xl text-gray-300 opacity-50"></i>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900">No Products Found</h3>
                                    <p class="text-gray-400 text-sm mt-1 mb-6">Your inventory is empty.</p>
                                    <a href="{{ route('products.create') }}" class="text-indigo-600 font-bold hover:underline">Add your first product</a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($products->hasPages())
            <div class="p-6 border-t border-gray-50 bg-gray-50/50">
                {{ $products->links() }}
            </div>
            @endif
        </div>
    </div>
</x-app-layout>