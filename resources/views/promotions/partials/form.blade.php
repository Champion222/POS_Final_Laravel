<div
    x-data="{
        type: @js(old('type', $promotion?->type ?? 'percent')),
        search: '',
        category: 'all',
        matchesProduct(name, categoryId, barcode) {
            const term = this.search.toLowerCase().trim();
            const matchesText = !term || name.includes(term) || (barcode && barcode.includes(term));
            const matchesCategory = this.category === 'all' || this.category == categoryId;
            return matchesText && matchesCategory;
        }
    }"
    class="space-y-8"
>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-[2rem] p-6 shadow-xl shadow-gray-100/60 border border-gray-100">
                <div class="flex items-center gap-3 mb-6">
                    <div class="h-11 w-11 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <i class="fas fa-percent"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Promotion Details</h3>
                        <p class="text-xs text-gray-400">Set discount and schedule.</p>
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Name</label>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name', $promotion?->name) }}"
                            required
                            placeholder="Summer Sale"
                            class="w-full px-4 py-3.5 rounded-xl bg-gray-50 border border-transparent text-sm font-bold text-gray-800 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition"
                        >
                        @error('name')
                            <p class="mt-2 text-xs font-bold text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Discount Type</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex items-center gap-2 px-4 py-3 rounded-xl border border-gray-200 bg-white text-xs font-bold text-gray-600 cursor-pointer transition">
                                <input type="radio" name="type" value="percent" class="sr-only" x-model="type" {{ old('type', $promotion?->type ?? 'percent') === 'percent' ? 'checked' : '' }}>
                                <span class="h-2 w-2 rounded-full bg-indigo-500" x-show="type === 'percent'"></span>
                                <span>Percent %</span>
                            </label>
                            <label class="flex items-center gap-2 px-4 py-3 rounded-xl border border-gray-200 bg-white text-xs font-bold text-gray-600 cursor-pointer transition">
                                <input type="radio" name="type" value="fixed" class="sr-only" x-model="type" {{ old('type', $promotion?->type ?? 'percent') === 'fixed' ? 'checked' : '' }}>
                                <span class="h-2 w-2 rounded-full bg-indigo-500" x-show="type === 'fixed'"></span>
                                <span>Fixed $</span>
                            </label>
                        </div>
                        @error('type')
                            <p class="mt-2 text-xs font-bold text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Discount Value</label>
                        <div class="relative">
                            <span x-show="type === 'fixed'" class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">$</span>
                            <input
                                type="number"
                                step="0.01"
                                min="0.01"
                                name="discount_value"
                                value="{{ old('discount_value', $promotion?->discount_value) }}"
                                required
                                :class="type === 'fixed' ? 'pl-8 pr-10' : 'pl-4 pr-10'"
                                class="w-full py-3.5 rounded-xl bg-gray-50 border border-transparent text-sm font-bold text-gray-800 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition"
                            >
                            <span x-show="type === 'percent'" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">%</span>
                        </div>
                        <p class="mt-2 text-[11px] text-gray-400 font-medium">
                            Use percent for percentage discounts or fixed to subtract a dollar value.
                        </p>
                        @error('discount_value')
                            <p class="mt-2 text-xs font-bold text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Start Date</label>
                            <input
                                type="date"
                                name="start_date"
                                value="{{ old('start_date', $promotion?->start_date?->toDateString()) }}"
                                required
                                class="w-full px-4 py-3.5 rounded-xl bg-gray-50 border border-transparent text-sm font-bold text-gray-800 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition"
                            >
                            @error('start_date')
                                <p class="mt-2 text-xs font-bold text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">End Date</label>
                            <input
                                type="date"
                                name="end_date"
                                value="{{ old('end_date', $promotion?->end_date?->toDateString()) }}"
                                required
                                class="w-full px-4 py-3.5 rounded-xl bg-gray-50 border border-transparent text-sm font-bold text-gray-800 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition"
                            >
                            @error('end_date')
                                <p class="mt-2 text-xs font-bold text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 rounded-2xl border border-gray-100 bg-gray-50">
                        <div>
                            <p class="text-sm font-bold text-gray-900">Promotion Status</p>
                            <p class="text-xs text-gray-400">Enable or pause this promotion.</p>
                        </div>
                        @php($isActive = old('is_active', $promotion?->is_active ?? true))
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ $isActive ? 'checked' : '' }}>
                            <div class="w-12 h-6 bg-gray-200 rounded-full peer peer-checked:bg-indigo-600 transition"></div>
                            <div class="absolute left-1 top-1 h-4 w-4 bg-white rounded-full transition peer-checked:translate-x-6"></div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-[2rem] p-6 shadow-xl shadow-gray-100/60 border border-gray-100">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Select Products</h3>
                        <p class="text-xs text-gray-400">Choose what gets discounted.</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="relative">
                            <i class="fas fa-search absolute left-4 top-3.5 text-gray-300"></i>
                            <input
                                type="text"
                                x-model="search"
                                placeholder="Search products"
                                class="w-full pl-10 pr-4 py-3 rounded-xl bg-gray-50 border border-transparent text-xs font-bold text-gray-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition"
                            >
                        </div>
                        <div class="relative">
                            <i class="fas fa-filter absolute left-4 top-3.5 text-gray-300"></i>
                            <select
                                x-model="category"
                                class="w-full pl-10 pr-8 py-3 rounded-xl bg-gray-50 border border-transparent text-xs font-bold text-gray-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition"
                            >
                                <option value="all">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach($products as $product)
                        @php($selected = in_array($product->id, old('products', $selectedProducts ?? []), true))
                        <label
                            x-show="matchesProduct('{{ strtolower($product->name) }}', '{{ $product->category_id }}', '{{ strtolower($product->barcode ?? '') }}')"
                            class="group relative cursor-pointer"
                        >
                            <input
                                type="checkbox"
                                name="products[]"
                                value="{{ $product->id }}"
                                class="peer sr-only"
                                {{ $selected ? 'checked' : '' }}
                            >
                            <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm transition-all duration-200 peer-checked:border-indigo-500 peer-checked:ring-2 peer-checked:ring-indigo-200 group-hover:shadow-md">
                                <div class="flex items-center gap-3">
                                    <div class="h-12 w-12 rounded-xl bg-gray-50 border border-gray-100 overflow-hidden shrink-0">
                                        @if($product->image)
                                            <img src="{{ asset('storage/'.$product->image) }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="h-full w-full flex items-center justify-center text-gray-300">
                                                <i class="fas fa-box"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-gray-900 truncate">{{ $product->name }}</p>
                                        <p class="text-[10px] text-gray-400 font-mono">{{ $product->barcode }}</p>
                                    </div>
                                </div>
                                <div class="mt-4 flex items-center justify-between text-xs font-bold text-gray-500">
                                    <span>${{ number_format($product->sale_price, 2) }}</span>
                                    <span class="px-2 py-1 rounded-lg bg-gray-50 border border-gray-100">Stock: {{ $product->qty }}</span>
                                </div>
                            </div>
                            <div class="absolute top-3 right-3 h-6 w-6 rounded-full bg-indigo-600 text-white text-xs flex items-center justify-center opacity-0 peer-checked:opacity-100 transition">
                                <i class="fas fa-check"></i>
                            </div>
                        </label>
                    @endforeach
                </div>

                @error('products')
                    <p class="mt-4 text-xs font-bold text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <div class="flex items-center justify-end">
        <button type="submit" class="bg-indigo-600 text-white px-8 py-3.5 rounded-xl font-bold text-sm shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 flex items-center gap-2">
            <i class="fas fa-save"></i> {{ $submitLabel }}
        </button>
    </div>
</div>
