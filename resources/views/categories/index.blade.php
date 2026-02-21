<x-app-layout>
    @section('header', 'Category Management')

    <div class="max-w-7xl mx-auto space-y-8">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-600 via-purple-600 to-indigo-800 p-4 lg:p-5 min-h-[132px] text-white shadow-2xl shadow-indigo-200 transition-transform hover:scale-[1.02] duration-300">
                <div class="relative z-10 flex flex-col justify-between h-full">
                    <div>
                        <div class="flex items-center gap-2.5 mb-1.5">
                            <div class="p-2 bg-white/10 rounded-lg backdrop-blur-sm">
                                <i class="fas fa-layer-group text-indigo-100 text-sm"></i>
                            </div>
                            <p class="text-indigo-100 font-bold text-xs uppercase tracking-widest">Total Categories</p>
                        </div>
                        <h2 class="text-4xl lg:text-[2.5rem] font-black tracking-tight mt-1">{{ $totalCategories }}</h2>
                    </div>
                    <div class="mt-3">
                        <span class="inline-flex items-center gap-1 text-xs font-medium bg-white/20 px-3 py-1 rounded-full backdrop-blur-md border border-white/10">
                            <i class="fas fa-chart-line"></i> Active Catalog
                        </span>
                    </div>
                </div>
                <div class="absolute -right-5 -top-5 h-24 w-24 rounded-full bg-white/10 blur-2xl"></div>
                <div class="absolute -left-5 -bottom-5 h-28 w-28 rounded-full bg-purple-500/20 blur-3xl"></div>
            </div>

            <div class="relative overflow-hidden rounded-3xl bg-white p-4 lg:p-5 min-h-[132px] border border-gray-100 shadow-xl shadow-gray-100/50 group hover:border-indigo-100 transition-all duration-300">
                <div class="absolute right-0 top-0 h-20 w-20 bg-gradient-to-br from-blue-50 to-transparent rounded-bl-[3rem] opacity-50 group-hover:scale-110 transition-transform"></div>
                
                <div class="relative z-10">
                    <div class="flex justify-between items-start mb-3">
                        <div class="p-2.5 bg-blue-50 text-blue-600 rounded-xl group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                            <i class="fas fa-box-open text-lg"></i>
                        </div>
                        <span class="text-xs font-bold text-gray-300 bg-gray-50 px-2 py-1 rounded-lg">Global</span>
                    </div>
                    <h2 class="text-3xl lg:text-4xl font-extrabold text-gray-900">{{ $totalProducts }}</h2>
                    <p class="text-sm text-gray-500 font-medium mt-1">Products Listed</p>
                </div>
            </div>

            <div class="relative overflow-hidden rounded-3xl bg-white p-4 lg:p-5 min-h-[132px] border border-gray-100 shadow-xl shadow-gray-100/50 flex flex-col justify-center items-center text-center group hover:border-emerald-100 transition-all duration-300">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                
                <div class="relative z-10">
                    <div class="relative mx-auto mb-3">
                        <div class="absolute inset-0 bg-emerald-400 blur-xl opacity-20 animate-pulse"></div>
                        <div class="relative h-12 w-12 lg:h-14 lg:w-14 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-xl lg:rounded-2xl flex items-center justify-center text-white text-lg lg:text-xl shadow-lg shadow-emerald-200">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                    <h3 class="text-base lg:text-lg font-bold text-gray-900">System Healthy</h3>
                    <p class="text-sm text-gray-400 font-medium mt-1">All categories active</p>
                </div>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8 items-start">
            
            <div class="w-full lg:w-2/3">
                <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-100/50 border border-gray-100 overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-50 flex flex-col sm:flex-row justify-between items-center gap-4 bg-white">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-1 rounded-full bg-indigo-500"></div>
                            <div>
                                <h3 class="font-bold text-xl text-gray-900">Category List</h3>
                                <p class="text-xs text-gray-400 font-medium">Manage your product groups</p>
                            </div>
                        </div>
                        <div class="relative group">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-300 group-focus-within:text-indigo-400 transition-colors"></i>
                            <input type="text" placeholder="Search..." class="pl-10 pr-4 py-2 bg-gray-50 border-none rounded-xl text-sm font-semibold focus:ring-2 focus:ring-indigo-100 focus:bg-white w-48 transition-all">
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-gray-50 text-xs font-bold text-gray-400 uppercase tracking-wider">
                                    <th class="px-8 py-5">Name</th>
                                    <th class="px-6 py-5 text-center">Products</th>
                                    <th class="px-6 py-5">Date Added</th>
                                    <th class="px-6 py-5 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($categories as $cat)
                                <tr class="group hover:bg-indigo-50/30 transition-colors duration-200">
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-4">
                                            <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-indigo-50 to-blue-50 border border-indigo-100/50 flex items-center justify-center text-indigo-600 font-black text-lg shadow-sm group-hover:scale-110 group-hover:shadow-md transition-all duration-300">
                                                {{ substr($cat->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-800 text-base group-hover:text-indigo-600 transition-colors">{{ $cat->name }}</p>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-500 border border-gray-200">
                                                    ID: {{ $cat->id }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        @if($cat->products_count > 0)
                                            <div class="inline-flex flex-col items-center justify-center h-10 min-w-[3rem] px-3 bg-indigo-50 text-indigo-700 rounded-xl border border-indigo-100">
                                                <span class="text-sm font-bold">{{ $cat->products_count }}</span>
                                            </div>
                                        @else
                                            <span class="text-xs font-bold text-gray-300 italic">Empty</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-2 text-sm font-medium text-gray-500">
                                            <i class="fas fa-calendar-alt text-gray-300"></i>
                                            {{ $cat->created_at->format('M d, Y') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-right">
                                        <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                            <button class="h-9 w-9 rounded-xl bg-white border border-gray-200 text-gray-400 hover:text-indigo-600 hover:border-indigo-200 hover:shadow-sm flex items-center justify-center transition-all">
                                                <i class="fas fa-pen text-xs"></i>
                                            </button>
                                            <form action="{{ route('categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Delete category {{ $cat->name }}?');">
                                                @csrf @method('DELETE')
                                                <button class="h-9 w-9 rounded-xl bg-white border border-gray-200 text-gray-400 hover:text-red-500 hover:border-red-200 hover:bg-red-50 flex items-center justify-center transition-all shadow-sm">
                                                    <i class="fas fa-trash-alt text-xs"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-16 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="h-20 w-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                                <i class="fas fa-folder-open text-3xl text-gray-300"></i>
                                            </div>
                                            <h3 class="text-lg font-bold text-gray-900">No Categories Found</h3>
                                            <p class="text-gray-400 text-sm max-w-xs mx-auto mt-1">Get started by creating your first product category on the right.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($categories->hasPages())
                    <div class="p-6 border-t border-gray-50 bg-gray-50/50">
                        {{ $categories->links() }}
                    </div>
                    @endif
                </div>
            </div>

            <div class="w-full lg:w-1/3 sticky top-24 space-y-6">
                
                <div class="bg-gray-900 rounded-[2rem] shadow-2xl shadow-gray-900/20 p-8 relative overflow-hidden text-white">
                    <div class="absolute top-0 right-0 -mr-10 -mt-10 h-40 w-40 bg-indigo-500 rounded-full blur-[60px] opacity-20"></div>
                    <div class="absolute bottom-0 left-0 -ml-10 -mb-10 h-40 w-40 bg-blue-500 rounded-full blur-[60px] opacity-20"></div>

                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="h-10 w-10 rounded-xl bg-white/10 backdrop-blur-md flex items-center justify-center border border-white/10">
                                <i class="fas fa-plus text-indigo-300"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg">Add New Category</h3>
                                <p class="text-xs text-gray-400">Organize your inventory</p>
                            </div>
                        </div>

                        <form action="{{ route('categories.store') }}" method="POST">
                            @csrf
                            <div class="space-y-5">
                                <div class="relative group">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Name</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="fas fa-tag text-gray-500 group-focus-within:text-indigo-400 transition-colors"></i>
                                        </div>
                                        <input type="text" name="name" required placeholder="e.g. Beverages" 
                                               class="w-full pl-11 pr-4 py-4 bg-white/5 border border-white/10 rounded-xl text-sm font-bold text-white placeholder-gray-500 focus:bg-white/10 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all outline-none">
                                    </div>
                                </div>

                                <button type="submit" class="group w-full py-4 bg-white text-gray-900 rounded-xl font-bold text-sm shadow-lg shadow-white/10 hover:bg-indigo-50 transition-all duration-300 flex items-center justify-center gap-2 transform active:scale-95">
                                    <span>Create Category</span>
                                    <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-indigo-50 to-white rounded-[2rem] p-6 border border-indigo-100 shadow-sm relative overflow-hidden">
                    <div class="relative z-10 flex gap-4">
                        <div class="h-10 w-10 rounded-full bg-indigo-100 flex-shrink-0 flex items-center justify-center text-indigo-600">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-indigo-900 text-sm">Pro Tip</h4>
                            <p class="text-xs text-indigo-800/70 mt-1 leading-relaxed">
                                Keep category names short and distinct. This helps cashiers find items faster during checkout.
                            </p>
                        </div>
                    </div>
                    <div class="absolute -right-4 -bottom-4 text-9xl text-indigo-500/5 rotate-12">
                        <i class="fas fa-tags"></i>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
