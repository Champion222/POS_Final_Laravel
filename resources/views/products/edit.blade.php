<x-app-layout>
    @section('header', 'Edit Product')

    <div class="max-w-5xl mx-auto pb-12">
        
        <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT')

            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="flex items-center gap-4">
                    <a href="{{ route('products.index') }}" class="group h-10 w-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-400 hover:text-indigo-600 hover:border-indigo-200 transition shadow-sm">
                        <i class="fas fa-arrow-left group-hover:-translate-x-0.5 transition-transform"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-black text-gray-900 tracking-tight">Edit Product</h1>
                        <p class="text-sm text-gray-500 font-medium">Updating details for <span class="text-indigo-600">{{ $product->name }}</span></p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('products.index') }}" class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-bold text-sm hover:bg-gray-50 transition">Cancel</a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 text-white font-bold text-sm shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition flex items-center gap-2">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2 space-y-8">
                    
                    <div class="bg-white rounded-[2rem] p-8 shadow-xl shadow-gray-100/50 border border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                            <i class="fas fa-info-circle text-indigo-500"></i> General Information
                        </h3>
                        
                        <div class="space-y-6">
                            <div class="group">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Product Name</label>
                                <input type="text" name="name" value="{{ $product->name }}" required 
                                       class="w-full px-4 py-3.5 bg-gray-50 border-transparent focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 rounded-xl font-bold text-gray-800 transition-all placeholder-gray-300">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="group">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Category</label>
                                    <div class="relative">
                                        <select name="category_id" class="w-full pl-4 pr-10 py-3.5 bg-gray-50 border-transparent focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 rounded-xl font-bold text-gray-700 transition-all appearance-none cursor-pointer">
                                            @foreach($categories as $cat)
                                                <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="absolute right-4 top-4 text-gray-400 pointer-events-none text-xs">
                                            <i class="fas fa-chevron-down"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="group">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Barcode / SKU</label>
                                    <div class="relative">
                                        <div class="absolute left-4 top-3.5 text-gray-400"><i class="fas fa-barcode"></i></div>
                                        <input type="text" name="barcode" value="{{ $product->barcode }}" required 
                                               class="w-full pl-10 pr-4 py-3.5 bg-gray-50 border-transparent focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 rounded-xl font-bold text-gray-800 transition-all font-mono">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-[2rem] p-8 shadow-xl shadow-gray-100/50 border border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                            <i class="fas fa-tags text-emerald-500"></i> Pricing & Inventory
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="group">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Cost Price</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-3.5 text-gray-400 font-bold">$</span>
                                    <input type="number" step="0.01" name="cost_price" value="{{ $product->cost_price }}" required 
                                           class="w-full pl-8 pr-4 py-3.5 bg-gray-50 border-transparent focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 rounded-xl font-bold text-gray-800 transition-all font-mono">
                                </div>
                            </div>

                            <div class="group">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Sale Price</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-3.5 text-emerald-500 font-bold">$</span>
                                    <input type="number" step="0.01" name="sale_price" value="{{ $product->sale_price }}" required 
                                           class="w-full pl-8 pr-4 py-3.5 bg-emerald-50/50 border-transparent focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 rounded-xl font-bold text-emerald-700 transition-all font-mono">
                                </div>
                            </div>

                            <div class="group">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Current Stock</label>
                                <div class="relative">
                                    <div class="absolute left-4 top-3.5 text-gray-400"><i class="fas fa-cubes text-xs"></i></div>
                                    <input type="number" name="qty" value="{{ $product->qty }}" required 
                                           class="w-full pl-10 pr-4 py-3.5 bg-gray-50 border-transparent focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 rounded-xl font-bold text-gray-800 transition-all font-mono">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="bg-white rounded-[2rem] p-8 shadow-xl shadow-gray-100/50 border border-gray-100 h-full flex flex-col">
                        <h3 class="text-lg font-bold text-gray-900 mb-6">Product Image</h3>
                        
                        <div class="flex-1 flex flex-col items-center justify-center mb-6">
                            <div class="relative group w-full aspect-square rounded-2xl border-2 border-dashed border-gray-300 bg-gray-50 overflow-hidden flex items-center justify-center hover:border-indigo-400 hover:bg-indigo-50 transition-all duration-300">
                                @if($product->image)
                                    <img id="preview-image" src="{{ asset('storage/' . $product->image) }}" class="w-full h-full object-contain p-4 transition-transform group-hover:scale-105 duration-500">
                                @else
                                    <div id="placeholder-icon" class="text-center">
                                        <i class="fas fa-image text-4xl text-gray-300 mb-2 group-hover:text-indigo-400 transition-colors"></i>
                                        <p class="text-xs text-gray-400 font-bold group-hover:text-indigo-500">No Image Set</p>
                                    </div>
                                @endif
                                
                                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 flex flex-col items-center justify-center transition-opacity duration-300 backdrop-blur-sm cursor-pointer" onclick="document.getElementById('file-upload').click()">
                                    <i class="fas fa-camera text-white text-3xl mb-2"></i>
                                    <span class="text-white text-xs font-bold uppercase tracking-wider">Change Photo</span>
                                </div>
                            </div>
                            <input id="file-upload" type="file" name="image" class="hidden" accept="image/*" onchange="previewFile(this)">
                        </div>

                        <p class="text-xs text-center text-gray-400 mb-4">
                            Supported: JPG, PNG, WEBP (Max 2MB)<br>
                            Click image to update.
                        </p>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <script>
        function previewFile(input) {
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    let img = document.getElementById('preview-image');
                    
                    // If no image existed before, we need to create the img element structure
                    if (!img) {
                        const container = input.parentElement.querySelector('.relative');
                        container.innerHTML = `<img id="preview-image" src="${e.target.result}" class="w-full h-full object-contain p-4 transition-transform group-hover:scale-105 duration-500">
                                               <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 flex flex-col items-center justify-center transition-opacity duration-300 backdrop-blur-sm cursor-pointer" onclick="document.getElementById('file-upload').click()">
                                                    <i class="fas fa-camera text-white text-3xl mb-2"></i>
                                                    <span class="text-white text-xs font-bold uppercase tracking-wider">Change Photo</span>
                                               </div>`;
                    } else {
                        img.src = e.target.result;
                    }
                }
                reader.readAsDataURL(file);
            }
        }
    </script>
</x-app-layout>