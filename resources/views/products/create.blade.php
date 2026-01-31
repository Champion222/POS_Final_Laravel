<x-app-layout>
    @section('header', 'Add New Product')

    <div class="max-w-6xl mx-auto pb-12">
        
        @if ($errors->any())
            <div class="mb-8 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl shadow-sm flex items-start gap-3">
                <div class="flex-shrink-0 text-red-500 mt-0.5"><i class="fas fa-exclamation-circle text-lg"></i></div>
                <div>
                    <h3 class="text-sm font-bold text-red-800">Please correct the following errors:</h3>
                    <ul class="mt-1 text-sm text-red-700 list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" x-data="productForm()">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2 space-y-8">
                    
                    <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-100/50 border border-gray-100 p-8">
                        <div class="flex items-center gap-3 mb-6 border-b border-gray-50 pb-4">
                            <div class="h-10 w-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg">
                                <i class="fas fa-box-open"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 text-lg">General Information</h3>
                                <p class="text-xs text-gray-400">Basic details about your item.</p>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div class="group">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Product Name</label>
                                <div class="relative">
                                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Premium Wireless Headphones" 
                                           class="w-full pl-4 pr-4 py-3.5 bg-gray-50 border-transparent focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 rounded-xl font-bold text-gray-800 transition-all placeholder-gray-300">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="group">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Category</label>
                                    <div class="relative">
                                        <select name="category_id" required class="w-full pl-4 pr-10 py-3.5 bg-gray-50 border-transparent focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 rounded-xl font-bold text-gray-700 transition-all appearance-none cursor-pointer">
                                            <option value="">Select Category...</option>
                                            @foreach($categories as $cat) 
                                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option> 
                                            @endforeach
                                        </select>
                                        <div class="absolute right-4 top-4 text-gray-400 pointer-events-none text-xs">
                                            <i class="fas fa-chevron-down"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="group">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Barcode / SKU</label>
                                    <div class="relative flex">
                                        <div class="absolute left-4 top-3.5 text-gray-400"><i class="fas fa-barcode"></i></div>
                                        <input type="text" name="barcode" x-model="barcodeValue" required placeholder="Scan or Generate" 
                                               class="w-full pl-10 pr-14 py-3.5 bg-gray-50 border-transparent focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 rounded-xl font-bold text-gray-800 transition-all placeholder-gray-300 font-mono">
                                        
                                        <button type="button" @click="generateBarcode()" class="absolute right-2 top-2 p-1.5 bg-white text-indigo-500 rounded-lg hover:bg-indigo-50 transition border border-gray-100 shadow-sm" title="Generate Random">
                                            <i class="fas fa-magic text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-100/50 border border-gray-100 p-8">
                        <div class="flex items-center gap-3 mb-6 border-b border-gray-50 pb-4">
                            <div class="h-10 w-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                                <i class="fas fa-tag"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 text-lg">Pricing & Stock</h3>
                                <p class="text-xs text-gray-400">Manage costs and inventory levels.</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Cost Price</label>
                                <div class="relative group">
                                    <span class="absolute left-4 top-3.5 text-gray-400 font-bold group-focus-within:text-indigo-500 transition-colors">$</span>
                                    <input type="number" step="0.01" name="cost_price" value="{{ old('cost_price') }}" required placeholder="0.00" 
                                           class="w-full pl-9 pr-4 py-3.5 bg-gray-50 border-transparent focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 rounded-xl font-bold text-gray-800 transition-all font-mono">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Sale Price</label>
                                <div class="relative group">
                                    <span class="absolute left-4 top-3.5 text-emerald-500 font-bold">$</span>
                                    <input type="number" step="0.01" name="sale_price" value="{{ old('sale_price') }}" required placeholder="0.00" 
                                           class="w-full pl-9 pr-4 py-3.5 bg-emerald-50/50 border-transparent focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 rounded-xl font-bold text-emerald-700 transition-all font-mono">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Initial Stock</label>
                                <div class="relative">
                                    <div class="absolute left-4 top-3.5 text-gray-400"><i class="fas fa-cubes text-xs"></i></div>
                                    <input type="number" name="qty" value="{{ old('qty', 0) }}" required placeholder="0" 
                                           class="w-full pl-10 pr-4 py-3.5 bg-gray-50 border-transparent focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 rounded-xl font-bold text-gray-800 transition-all font-mono">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1 space-y-6">
                    
                    <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-100/50 border border-gray-100 p-8">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold text-gray-900 text-lg">Product Image</h3>
                            <span class="bg-indigo-50 text-indigo-600 text-[10px] font-bold px-2 py-1 rounded-lg uppercase">Optional</span>
                        </div>

                        <div class="relative w-full h-72 rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50/50 hover:bg-white hover:border-indigo-400 hover:shadow-lg transition-all duration-300 overflow-hidden group flex flex-col items-center justify-center text-center">
                            
                            <template x-if="imageUrl">
                                <div class="absolute inset-0 w-full h-full bg-white">
                                    <img :src="imageUrl" class="w-full h-full object-contain p-4">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-sm">
                                        <p class="text-white font-bold text-sm"><i class="fas fa-edit mr-2"></i>Change Image</p>
                                    </div>
                                </div>
                            </template>
                            
                            <template x-if="!imageUrl">
                                <div class="p-6 pointer-events-none">
                                    <div class="w-16 h-16 bg-white rounded-full shadow-sm flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                                        <i class="fas fa-cloud-upload-alt text-2xl text-indigo-400"></i>
                                    </div>
                                    <p class="text-sm font-bold text-gray-600 group-hover:text-indigo-600 transition-colors">Click to upload image</p>
                                    <p class="text-xs text-gray-400 mt-1">SVG, PNG, JPG (Max 2MB)</p>
                                </div>
                            </template>

                            <input type="file" name="image" accept="image/*" @change="fileChosen" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        </div>
                        
                        <template x-if="imageUrl">
                            <button type="button" @click="clearImage()" class="mt-4 w-full py-3 text-xs font-bold text-red-500 bg-red-50 rounded-xl hover:bg-red-100 transition flex items-center justify-center gap-2">
                                <i class="fas fa-trash-alt"></i> Remove Image
                            </button>
                        </template>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="group w-full py-4 bg-gray-900 text-white rounded-2xl font-bold shadow-xl shadow-gray-900/20 hover:bg-black hover:scale-[1.02] active:scale-95 transition-all duration-300 flex items-center justify-center gap-3 text-base">
                            <span>Save Product</span>
                            <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                        </button>
                        <a href="{{ route('products.index') }}" class="block text-center mt-4 text-xs font-bold text-gray-400 hover:text-gray-600 transition">Cancel and go back</a>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <script>
        function productForm() {
            return {
                barcodeValue: '',
                imageUrl: null,

                generateBarcode() {
                    const randomPart = Math.floor(Math.random() * 900000000000) + 100000000000;
                    this.barcodeValue = '2' + randomPart.toString();
                },

                fileChosen(event) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => { this.imageUrl = e.target.result; };
                        reader.readAsDataURL(file);
                    }
                },

                clearImage() {
                    this.imageUrl = null;
                    document.querySelector('input[type="file"][name="image"]').value = '';
                }
            }
        }
    </script>
</x-app-layout>