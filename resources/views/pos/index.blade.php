<x-app-layout>
    @section('header', 'POS Terminal')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="grid h-[calc(100vh-7rem)] grid-cols-1 gap-5 p-1 lg:grid-cols-[minmax(0,1fr)_22rem] xl:grid-cols-[minmax(0,1fr)_24rem]" x-data="posSystem()" x-cloak>
        <div x-show="stockAlertOpen"
             x-transition:enter="transform ease-out duration-300 transition"
             x-transition:enter-start="translate-y-2 opacity-0"
             x-transition:enter-end="translate-y-0 opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed top-24 right-6 z-[90] w-full max-w-sm bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-white/30 ring-1 ring-black/5 overflow-hidden">
            <div class="flex items-start gap-4 p-4">
                <div class="h-10 w-10 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center shrink-0">
                    <i class="fas fa-triangle-exclamation text-lg"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-black text-gray-900">Stock Alert</p>
                    <p class="text-xs font-medium text-gray-500 mt-1" x-text="stockAlertMessage"></p>
                </div>
                <button type="button" @click="stockAlertOpen = false" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="h-1 bg-gradient-to-r from-amber-400 via-orange-400 to-pink-500"></div>
        </div>
        
        <div class="min-w-0 flex h-full flex-col gap-5">
            
            <div class="relative z-20 flex shrink-0 flex-col items-center gap-3 rounded-[2rem] border border-gray-100 bg-white p-4 shadow-xl shadow-gray-100/50 md:flex-row">
                <div class="flex-1 relative w-full">
                    <i class="fas fa-search absolute left-5 top-4 text-indigo-300 text-lg"></i>
                    <input x-model="search" type="text" placeholder="Search products, barcodes..."
                           @input.debounce.150="tryAddBarcode()"
                           @keydown.enter.prevent="tryAddBarcode()"
                           class="w-full pl-14 pr-6 py-3.5 bg-gray-50 border-transparent rounded-2xl font-bold text-gray-700 focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition-all placeholder-gray-400">
                </div>
                <div class="relative w-full md:w-64">
                    <i class="fas fa-filter absolute left-5 top-4 text-indigo-300"></i>
                    <select x-model="category" class="w-full pl-12 pr-10 py-3.5 bg-gray-50 border-transparent rounded-2xl font-bold text-gray-700 cursor-pointer focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition-all appearance-none">
                        <option value="all">All Categories</option>
                        @foreach($categories as $cat) <option value="{{ $cat->id }}">{{ $cat->name }}</option> @endforeach
                    </select>
                </div>
            </div>

            <div class="custom-scrollbar flex-1 overflow-y-auto pr-1 pb-4">
                <div class="grid grid-cols-2 gap-4 md:grid-cols-3 xl:grid-cols-5">
                    @forelse($products as $product)
                    <div x-show="matchesSearch('{{ strtolower($product->name) }}', '{{ $product->category_id }}', '{{ $product->barcode }}')"
                         @click.stop.prevent="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->sale_price }}, '{{ $product->image }}')"
                         :class="(productStock[{{ $product->id }}] ?? 0) <= 0 ? 'opacity-50 pointer-events-none' : ''"
                         class="group relative flex h-full cursor-pointer select-none flex-col overflow-hidden rounded-[1.8rem] border border-gray-100 bg-white p-3 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-indigo-100/50">
                        
                        <div class="relative mb-3 h-32 overflow-hidden rounded-[1.2rem] bg-gray-50 transition-colors group-hover:bg-indigo-50">
                            @if($product->image) 
                                <img src="{{ asset('storage/'.$product->image) }}" loading="lazy" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                            @else 
                                <div class="w-full h-full flex items-center justify-center text-gray-300 group-hover:text-indigo-300 transition-colors">
                                    <i class="fas fa-box text-4xl"></i>
                                </div> 
                            @endif
                            
                            <div class="absolute top-2 left-2 rounded-lg border border-white/20 px-2 py-0.5 text-[10px] font-black uppercase tracking-wider shadow-sm backdrop-blur-md"
                                 :class="(productStock[{{ $product->id }}] ?? 0) <= 5 ? 'bg-red-500/90 text-white' : 'bg-white/90 text-gray-800'">
                                <span x-text="(productStock[{{ $product->id }}] ?? {{ $product->qty }}) + ' Left'">{{ $product->qty }} Left</span>
                            </div>

                            <div x-show="promotionIndex[{{ $product->id }}]" class="absolute top-2 right-2 rounded-lg bg-emerald-500/90 px-2 py-0.5 text-[10px] font-black uppercase tracking-wider text-white shadow-sm">
                                <span x-text="promotionIndex[{{ $product->id }}]?.label + ' OFF'"></span>
                            </div>
                        </div>

                        <div class="flex-1 flex flex-col justify-between">
                            <div>
                                <h3 class="mb-1 line-clamp-2 text-sm font-bold leading-snug text-gray-800 transition-colors group-hover:text-indigo-600">{{ $product->name }}</h3>
                                <p class="mb-2 text-[10px] font-mono text-gray-400">{{ $product->barcode }}</p>
                            </div>

                            <div class="mt-auto flex items-center justify-between border-t border-dashed border-gray-100 pt-2">
                                <div class="flex items-baseline gap-2">
                                    <span class="text-base font-black text-indigo-600">$<span x-text="formatMoney(finalPriceFor({{ $product->id }}, {{ $product->sale_price }}))">{{ number_format($product->sale_price, 2) }}</span></span>
                                    <span x-show="promotionIndex[{{ $product->id }}]" class="text-xs text-gray-400 font-bold line-through">${{ number_format($product->sale_price, 2) }}</span>
                                </div>
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 text-gray-400 shadow-sm transition-all group-hover:bg-indigo-600 group-hover:text-white">
                                    <i class="fas fa-plus"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty 
                        <div class="col-span-full flex flex-col items-center justify-center text-gray-400 py-20">
                            <i class="fas fa-search text-6xl mb-4 opacity-20"></i>
                            <p class="font-bold">No products found.</p>
                        </div> 
                    @endforelse
                </div>
            </div>
        </div>

        <div class="relative z-30 flex h-full min-w-0 flex-col overflow-hidden rounded-[2rem] border border-gray-100 bg-white shadow-2xl shadow-gray-200/50">
            
            <div x-show="mode === 'cart'" class="flex flex-col h-full relative z-10" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0">
                
                <div class="z-20 flex flex-col gap-3 border-b border-gray-50 bg-white px-4 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-black tracking-tight text-gray-900">Current Order</h2>
                        <p class="text-xs text-gray-400 font-medium mt-0.5" x-text="cart.length + ' items added'"></p>
                    </div>
                    <div class="flex items-center gap-3">
                        
                        <button @click="openClearModal()" x-show="cart.length > 0" class="flex items-center gap-2 rounded-xl bg-red-50 px-3 py-1.5 text-[11px] font-bold text-red-500 transition hover:bg-red-100">
                            <i class="fas fa-trash-alt"></i> Clear
                        </button>
                    </div>
                </div>

                <div class="shrink-0 px-4 pt-3">
                    <div class="relative">
                        <i class="fas fa-user-circle absolute left-4 top-3 text-indigo-300 text-base"></i>
                        <select id="customer_id" class="w-full appearance-none rounded-xl border-transparent bg-gray-50 py-2.5 pl-11 pr-4 text-sm font-bold text-gray-700 transition-all focus:bg-white focus:ring-2 focus:ring-indigo-100">
                            <option value="">Guest Customer</option>
                            @foreach($customers as $customer) <option value="{{ $customer->id }}">{{ $customer->name }}</option> @endforeach
                        </select>
                        
                    </div>
                </div>

                <div class="custom-scrollbar flex-1 space-y-3 overflow-y-auto p-4" id="cart-scroll">
                    <template x-if="cart.length === 0">
                        <div class="h-full flex flex-col items-center justify-center text-center select-none">
                            <div class="mb-3 flex h-20 w-20 items-center justify-center rounded-full bg-gray-50">
                                <i class="fas fa-shopping-basket text-4xl text-gray-200"></i>
                            </div>
                            <h3 class="text-base font-bold text-gray-900">Your cart is empty</h3>
                            <p class="mt-1 max-w-[200px] text-xs text-gray-400">Scan a barcode or click a product to add it here.</p>
                        </div>
                    </template>
                    
                    <template x-for="(item, index) in cart" :key="item.id">
                        <div class="group flex items-center justify-between rounded-2xl border border-transparent p-2.5 transition-all hover:border-gray-100 hover:bg-gray-50">
                            <div class="flex items-center gap-4 overflow-hidden">
                                <div class="h-12 w-12 shrink-0 overflow-hidden rounded-xl border border-gray-100 bg-gray-50">
                                    <img :src="item.image ? '/storage/'+item.image : ''" class="w-full h-full object-cover">
                                </div>
                                <div class="min-w-0">
                                    <h4 class="truncate text-sm font-bold text-gray-800" x-text="item.name"></h4>
                                    <div class="flex items-center gap-2 text-xs font-bold text-indigo-500 mt-1">
                                        <span>$<span x-text="item.finalPrice.toFixed(2)"></span></span>
                                        <span x-show="item.discount > 0" class="text-[10px] text-gray-400 font-bold line-through">$<span x-text="item.basePrice.toFixed(2)"></span></span>
                                    </div>
                                    <div x-show="item.promotionLabel" class="mt-1 inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-bold uppercase tracking-wide bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        <i class="fas fa-tag text-[9px]"></i>
                                        <span x-text="item.promotionLabel + ' OFF'"></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-1.5 rounded-xl border border-gray-200 bg-white p-1 shadow-sm">
                                <button @click.stop="updateQty(index, -1)" class="flex h-6 w-6 items-center justify-center rounded-lg text-gray-400 transition hover:bg-red-50 hover:text-red-500"><i class="fas fa-minus text-[10px]"></i></button>
                                <span class="w-4 select-none text-center text-xs font-bold text-gray-900" x-text="item.qty"></span>
                                <button @click.stop="updateQty(index, 1)" class="flex h-6 w-6 items-center justify-center rounded-lg text-gray-400 transition hover:bg-indigo-50 hover:text-indigo-600"><i class="fas fa-plus text-[10px]"></i></button>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="relative z-50 border-t border-gray-50 bg-white p-4 shadow-[0_-10px_40px_rgba(0,0,0,0.03)]">
                    
                    <div class="mb-4 space-y-1.5">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-400 font-bold">Subtotal</span>
                            <span class="text-sm font-bold text-gray-800">$<span x-text="subtotal.toFixed(2)"></span></span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-400 font-bold">Promotion Discount</span>
                            <span class="text-sm font-bold text-emerald-600">-$<span x-text="discountTotal.toFixed(2)"></span></span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-400 font-bold">Tax (0%)</span>
                            <span class="font-bold text-gray-800">$0.00</span>
                        </div>
                        <div class="my-2 h-px bg-gray-100"></div>
                        <div class="flex justify-between items-end">
                            <span class="text-base font-black text-gray-800">Total Amount</span>
                            <span class="text-3xl font-black tracking-tight text-indigo-600">$<span x-text="total.toFixed(2)"></span></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <button type="button"
                                @click="processPayment('cash')"
                                :disabled="isSaving || cart.length === 0"
                                :class="isSaving || cart.length === 0 ? 'opacity-50 cursor-not-allowed pointer-events-none' : ''"
                                class="group flex items-center justify-center gap-2 rounded-2xl bg-gray-100 py-3 text-sm font-black text-gray-800 transition-all hover:bg-gray-200 active:scale-95">
                            <i class="fas fa-money-bill-wave text-base text-gray-400 group-hover:text-gray-600"></i> Cash
                        </button>
                        <button type="button"
                                @click="processPayment('qr')"
                                :disabled="isSaving || cart.length === 0"
                                :class="isSaving || cart.length === 0 ? 'opacity-50 cursor-not-allowed pointer-events-none' : ''"
                                class="group flex items-center justify-center gap-2 rounded-2xl bg-[#E1232E] py-3 text-sm font-black text-white shadow-xl shadow-red-200 transition-all hover:bg-[#d31923] active:scale-95">
                            <i class="fas fa-qrcode text-base text-red-100 group-hover:text-white"></i> KHQR Pay
                        </button>
                    </div>
                </div>
            </div>

            <div x-show="mode === 'qr'" x-cloak 
                 class="absolute inset-0 z-50 flex h-full flex-col bg-[#f2f4f8] text-gray-900"
                 x-transition:enter="transition ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-full" 
                 x-transition:enter-end="opacity-100 translate-y-0">
                
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4 sm:px-6">
                    <div class="flex items-center gap-3">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#E1232E]/10 text-[#E1232E]">
                            <i class="fas fa-qrcode text-base"></i>
                        </span>
                        <div>
                            <h2 class="text-base font-black leading-tight">KHQR Payment</h2>
                            <p class="text-[11px] font-semibold text-slate-500">Scan to pay with Bakong app or any bank apps</p>
                        </div>
                    </div>
                    <button @click="cancelQR()" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-bold text-slate-600 transition hover:bg-slate-100">Cancel</button>
                </div>

                <div class="flex flex-1 flex-col items-center justify-center gap-4 overflow-y-auto px-4 py-6 sm:px-6">
                    <div class="khqr-card w-full max-w-[20rem] sm:max-w-[22rem]">
                        <div class="khqr-card-header">
                            <span class="khqr-wordmark">KHQR</span>
                            <span class="khqr-header-cut"></span>
                        </div>
                        <div class="khqr-card-body">
                            <p class="khqr-merchant-name">{{ config('services.bakong_khqr.merchant_name', config('app.name')) }}</p>
                            <div class="khqr-amount-row">
                                <span class="khqr-amount-value" x-text="formattedKhqrAmount"></span>
                                <span class="khqr-currency">USD</span>
                            </div>
                        </div>
                        <div class="khqr-separator"></div>
                        <div class="khqr-qr-wrap">
                            <div x-show="loading" class="flex h-full min-h-[188px] flex-col items-center justify-center gap-2 text-slate-400">
                                <i class="fas fa-circle-notch fa-spin text-3xl text-[#E1232E]"></i>
                                <span class="text-[11px] font-black tracking-[0.2em] text-slate-500">GENERATING QR</span>
                            </div>
                            <div x-show="!loading" class="khqr-qr-box">
                                <div x-html="qrCode" class="h-full w-full [&>svg]:h-full [&>svg]:w-full"></div>
                                <div class="khqr-qr-center-icon">
                                    <span>$</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p x-show="bakongError" class="text-center text-xs font-bold text-red-500" x-text="bakongError"></p>

                    <p class="text-center text-[11px] font-semibold text-slate-500" x-text="formatTime(timeLeft) + ' | QR will be expired'"></p>

                    <div class="flex w-full max-w-[20rem] items-center gap-3 text-xs font-bold uppercase tracking-widest text-slate-400">
                        <span class="h-px flex-1 bg-slate-200"></span>
                        Or
                        <span class="h-px flex-1 bg-slate-200"></span>
                    </div>

                    <button type="button" class="w-full max-w-[20rem] rounded-xl bg-[#E1232E] py-3 text-sm font-black text-white shadow-lg shadow-red-200/70 transition hover:bg-[#d31923]">
                        Pay via Bakong App
                    </button>
                </div>
            </div>

            <div x-show="mode === 'processing'" x-cloak
                 class="flex flex-col h-full bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 text-white absolute inset-0 z-[55] items-center justify-center text-center p-8"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">

                <div class="h-28 w-28 rounded-full border-4 border-white/20 border-t-cyan-400 flex items-center justify-center mb-8 animate-spin">
                    <i class="fas fa-circle-notch text-3xl text-cyan-300"></i>
                </div>
                <h1 class="text-4xl font-black mb-3 tracking-tight">Processing Payment...</h1>
                <p class="text-slate-200 text-base font-medium">Please wait while we save your order.</p>
            </div>

            <div x-show="mode === 'success'" x-cloak 
                 class="flex flex-col h-full bg-emerald-500 text-white absolute inset-0 z-[60] items-center justify-center text-center p-8"
                 x-transition:enter="transition ease-out duration-500" 
                 x-transition:enter-start="opacity-0 scale-90" 
                 x-transition:enter-end="opacity-100 scale-100">
                
                <div class="w-32 h-32 bg-white rounded-full flex items-center justify-center shadow-2xl mb-8 animate-bounce">
                    <i class="fas fa-check text-6xl text-emerald-500"></i>
                </div>
                <h1 class="text-5xl font-black mb-4 tracking-tight">Success!</h1>
                <p class="text-emerald-100 text-xl font-medium opacity-90">Payment Verified & Order Saved</p>
            </div>
        </div>

        <div x-show="clearModalOpen" x-cloak class="fixed inset-0 z-[80] flex items-center justify-center p-4" @keydown.escape.window="closeClearModal()">
            <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeClearModal()"></div>
            <div class="relative w-full max-w-md bg-white rounded-[2rem] p-6 shadow-2xl border border-gray-100" @click.stop>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-black text-gray-900 tracking-tight">Clear Cart?</h2>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mt-1">This will remove all items.</p>
                    </div>
                    <button type="button" @click="closeClearModal()" class="w-9 h-9 rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-600 transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="mt-6 grid grid-cols-2 gap-4">
                    <button type="button" @click="closeClearModal()" class="py-3 rounded-2xl bg-gray-100 text-gray-700 font-black text-sm hover:bg-gray-200 transition">
                        Cancel
                    </button>
                    <button type="button" @click="confirmClearCart()" class="py-3 rounded-2xl bg-red-600 text-white font-black text-sm hover:bg-red-700 transition">
                        Confirm
                    </button>
                </div>
            </div>
        </div>

        <div x-show="cashModalOpen" x-cloak class="fixed inset-0 z-[80] flex items-center justify-center p-4" @keydown.escape.window="closeCashModal()">
            <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeCashModal()"></div>
            <div class="relative w-full max-w-xl bg-white rounded-[2rem] p-6 shadow-2xl border border-gray-100" @click.stop>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-black text-gray-900 tracking-tight">Cash Payment</h2>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mt-1">Confirm the cash amount</p>
                    </div>
                    <button type="button" @click="closeCashModal()" class="w-9 h-9 rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-600 transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="mt-6 rounded-2xl border border-gray-100 bg-gray-50 p-4 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Amount</p>
                        <h3 class="text-3xl font-black text-indigo-600">$<span x-text="total.toFixed(2)"></span></h3>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Change</p>
                        <h3 class="text-2xl font-black" :class="changeDue >= 0 ? 'text-emerald-600' : 'text-red-500'">
                            $<span x-text="changeDue.toFixed(2)"></span>
                        </h3>
                    </div>
                </div>

                <div x-show="cart.length === 0" class="mt-4 rounded-2xl border border-red-100 bg-red-50 p-3 text-xs font-bold text-red-600">
                    Cart is empty.
                </div>

                <div x-show="cart.length > 0" class="mt-6 space-y-2">
                    <label for="cash_received" class="text-xs font-bold text-gray-400 uppercase tracking-widest">Cash Received</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">$</span>
                        <input id="cash_received" x-ref="cashInput" type="number" min="0" step="0.01" x-model.number="cashReceived"
                               @input="cashError = ''"
                               class="w-full pl-9 pr-4 py-3.5 rounded-2xl bg-white border border-gray-200 text-gray-800 font-bold focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition"
                               placeholder="0.00">
                    </div>
                    <p x-show="cashError" class="text-xs font-bold text-red-500" x-text="cashError"></p>
                </div>

                <div x-show="cart.length > 0" class="mt-8 grid grid-cols-2 gap-4">
                    <button type="button" @click="closeCashModal()" class="py-3 rounded-2xl bg-gray-100 text-gray-700 font-black text-sm hover:bg-gray-200 transition">
                        Cancel
                    </button>
                    <button type="button"
                            @click="confirmCashPayment()"
                            :disabled="isSaving"
                            :class="isSaving ? 'opacity-60 cursor-not-allowed' : ''"
                            class="py-3 rounded-2xl bg-emerald-600 text-white font-black text-sm hover:bg-emerald-700 transition flex items-center justify-center gap-2">
                        <span x-show="!isSaving">Confirm Cash</span>
                        <span x-show="isSaving" class="inline-flex items-center gap-2">
                            <i class="fas fa-circle-notch fa-spin"></i> Processing...
                        </span>
                    </button>
                </div>

                <div x-show="cart.length === 0" class="mt-8">
                    <button type="button" @click="closeCashModal()" class="w-full py-3 rounded-2xl bg-gray-100 text-gray-700 font-black text-sm hover:bg-gray-200 transition">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function posSystem() {
            return {
                search: '', category: 'all', cart: [], mode: 'cart', loading: false, qrCode: '', qrMd5: '', 
                bakongError: '', countdownTimer: null, timeLeft: 180, isPolling: false, syncTimer: null,
                stockAlertOpen: false, stockAlertMessage: '', stockAlertTimer: null,
                clearModalOpen: false,
                cashModalOpen: false, cashReceived: null, cashError: '', isSaving: false,
                productStock: @json($products->pluck('qty', 'id')),
                barcodeIndex: @json($barcodeIndex),
                promotionIndex: @json($promotionIndex),
                routes: {
                    generateQr: @json(route('pos.generate_qr')),
                    checkQr: @json(route('pos.check_qr')),
                    stock: @json(route('pos.products.stock')),
                    promotions: @json(route('pos.products.promotions')),
                    store: @json(route('pos.store')),
                },

                init() {
                    this.refreshStock();
                    this.refreshPromotions();
                    this.syncTimer = setInterval(() => {
                        this.refreshStock();
                        this.refreshPromotions();
                    }, 30000);
                },

                get subtotalCents() {
                    return this.cart.reduce((sum, item) => {
                        const priceCents = Math.round(Number(item.basePrice || 0) * 100);
                        const qty = Number(item.qty || 0);
                        return sum + (priceCents * qty);
                    }, 0);
                },
                get discountCents() {
                    return this.cart.reduce((sum, item) => {
                        const discountCents = Math.round(Number(item.discount || 0) * 100);
                        const qty = Number(item.qty || 0);
                        return sum + (discountCents * qty);
                    }, 0);
                },
                get totalCents() { return Math.max(0, this.subtotalCents - this.discountCents); },
                get subtotal() { return this.subtotalCents / 100; },
                get discountTotal() { return this.discountCents / 100; },
                get total() { return this.totalCents / 100; },
                get changeDue() { return Number(this.cashReceived || 0) - this.total; },
                get formattedKhqrAmount() {
                    return new Intl.NumberFormat('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2,
                    }).format(this.total || 0);
                },
                
                matchesSearch(name, cat, code) {
                    const s = this.search.toLowerCase();
                    return (name.includes(s) || (code && code.includes(s))) && (this.category === 'all' || this.category == cat);
                },
                formatMoney(value) {
                    return Number(value || 0).toFixed(2);
                },
                showStockAlert(message) {
                    this.stockAlertMessage = message;
                    this.stockAlertOpen = true;
                    if (this.stockAlertTimer) {
                        clearTimeout(this.stockAlertTimer);
                    }
                    this.stockAlertTimer = setTimeout(() => {
                        this.stockAlertOpen = false;
                    }, 3500);
                },
                finalPriceFor(productId, basePrice) {
                    const promotion = this.promotionIndex[productId];
                    if (!promotion) {
                        return Number(basePrice || 0);
                    }
                    const discount = Math.min(Number(promotion.discount_amount || 0), Number(basePrice || 0));
                    return Math.max(0, Number(basePrice || 0) - discount);
                },
                applyPromotionToItem(item) {
                    const promotion = this.promotionIndex[item.id];
                    const basePrice = Number(item.basePrice || 0);
                    const discount = promotion ? Math.min(Number(promotion.discount_amount || 0), basePrice) : 0;
                    const finalPrice = Math.max(0, basePrice - discount);
                    return {
                        ...item,
                        basePrice,
                        discount,
                        finalPrice,
                        promotionLabel: promotion ? promotion.label : null,
                    };
                },
                applyPromotionsToCart() {
                    this.cart = this.cart.map((item) => this.applyPromotionToItem(item));
                },
                tryAddBarcode() {
                    const code = this.search.trim().toLowerCase();
                    if (!code) {
                        return;
                    }

                    const product = this.barcodeIndex[code];
                    if (!product) {
                        return;
                    }

                    this.addToCart(product.id, product.name, product.price, product.image);
                    this.search = '';
                },
                addToCart(id, name, price, image) {
                    const itemId = Number(id);
                    const basePrice = Number(price);
                    let index = this.cart.findIndex(i => Number(i.id) === itemId);
                    const available = Number(this.productStock[itemId] ?? 0);

                    if (available <= 0) {
                        this.showStockAlert('Out of stock.');
                        return;
                    }

                    if (index !== -1) {
                        const currentQty = Number(this.cart[index].qty || 0);
                        if (currentQty + 1 > available) {
                            this.showStockAlert(`Only ${available} left in stock.`);
                            return;
                        }
                        this.cart.splice(index, 1, this.applyPromotionToItem({ ...this.cart[index], qty: currentQty + 1 }));
                        return;
                    }

                    this.cart.push(this.applyPromotionToItem({ id: itemId, name, basePrice, qty: 1, image }));
                    this.$nextTick(() => { const el = document.getElementById('cart-scroll'); if (el) el.scrollTop = el.scrollHeight; });
                },
                updateQty(index, val) {
                    if (!this.cart[index]) {
                        return;
                    }

                    let newQty = Number(this.cart[index].qty || 0) + val;
                    if (val > 0) {
                        const itemId = Number(this.cart[index].id);
                        const available = Number(this.productStock[itemId] ?? 0);
                        if (newQty > available) {
                            this.showStockAlert(`Only ${available} left in stock.`);
                            return;
                        }
                    }
                    if (newQty <= 0) {
                        this.cart.splice(index, 1);
                        return;
                    }

                    this.cart.splice(index, 1, this.applyPromotionToItem({ ...this.cart[index], qty: newQty }));
                },
                openClearModal() {
                    if (this.cart.length === 0) {
                        return;
                    }
                    this.clearModalOpen = true;
                },
                closeClearModal() {
                    this.clearModalOpen = false;
                },
                confirmClearCart() {
                    this.cart = [];
                    this.clearModalOpen = false;
                },

                resolveUrl(value) {
                    try {
                        const current = window.location;
                        const url = new URL(value, current.origin);
                        url.protocol = current.protocol;
                        url.host = current.host;
                        return url.toString();
                    } catch (error) {
                        return value;
                    }
                },
                
                processPayment(type) {
                    if (this.isSaving) {
                        return;
                    }

                    if (this.cart.length === 0) {
                        alert('Cart is empty!');
                        return;
                    }

                    if (type === 'cash') {
                        this.openCashModal();
                        return;
                    }

                    if (type === 'qr') {
                        this.startQrPayment();
                    }
                },

                startQrPayment() {
                    const amount = Number(this.total.toFixed(2));
                    if (amount <= 0) {
                        alert('Cart is empty!');
                        return;
                    }

                    this.mode = 'qr';
                    this.loading = true;
                    this.timeLeft = 180;
                    this.bakongError = '';

                    fetch(this.resolveUrl(this.routes.generateQr), {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify({ amount: amount.toFixed(2) })
                    })
                    .then(async (r) => {
                        if (!r.ok) {
                            const message = await r.text();
                            throw new Error(message || `HTTP ${r.status}`);
                        }

                        return r.json();
                    })
                    .then(d => {
                        if (d.status !== 'success') {
                            this.loading = false;
                            this.bakongError = 'Bakong error: Unable to generate QR.';
                            return;
                        }
                        this.qrCode = d.qr_svg;
                        this.qrMd5 = d.md5;
                        this.loading = false;
                        this.isPolling = true; 
                        this.startPolling(); 
                    })
                    .catch((error) => {
                        this.loading = false;
                        this.bakongError = `Bakong error: ${error?.message || 'Failed to fetch'}`;
                        this.isPolling = false;
                    });
                },

                cancelQR() { 
                    this.isPolling = false; 
                    clearInterval(this.countdownTimer);
                    this.bakongError = '';
                    this.mode = 'cart'; 
                },

                openCashModal() {
                    this.cashReceived = null;
                    this.cashError = '';
                    this.cashModalOpen = true;
                    this.$nextTick(() => {
                        if (this.$refs.cashInput) {
                            this.$refs.cashInput.focus();
                        }
                    });
                },

                closeCashModal() {
                    this.cashModalOpen = false;
                    this.cashError = '';
                },

                confirmCashPayment() {
                    if (this.isSaving) {
                        return;
                    }

                    if (this.cart.length === 0) {
                        this.cashError = 'Cart is empty.';
                        return;
                    }

                    const received = Number(this.cashReceived);

                    if (Number.isNaN(received) || received <= 0) {
                        this.cashError = 'Enter a valid cash amount.';
                        return;
                    }

                    if (received < this.total) {
                        this.cashError = 'Cash received must cover the total amount.';
                        return;
                    }

                    this.cashModalOpen = false;
                    this.cashError = '';
                    this.saveOrder('cash');
                },

                async startPolling() {
                    this.countdownTimer = setInterval(() => {
                        if(this.timeLeft > 0) this.timeLeft--;
                        else { this.cancelQR(); alert("Timeout - Payment Cancelled"); }
                    }, 1000);

                    while (this.isPolling && this.mode === 'qr') {
                        try {
                            const res = await fetch(this.resolveUrl(this.routes.checkQr), {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                                body: JSON.stringify({ md5: this.qrMd5 })
                            });
                            const data = await res.json();

                    if (data.responseCode === 0) { 
                        this.isPolling = false; 
                        clearInterval(this.countdownTimer);
                        this.saveOrder('qr'); 
                        return; 
                    }
                        } catch (error) {
                            this.bakongError = `Bakong error: ${error?.message || 'Failed to fetch'}`;
                            console.log("Check failed, retrying...");
                        }
                        await new Promise(r => setTimeout(r, 1500));
                    }
                },

                formatTime(seconds) {
                    const m = Math.floor(seconds / 60).toString().padStart(2, '0');
                    const s = (seconds % 60).toString().padStart(2, '0');
                    return `${m}:${s}`;
                },

                wait(ms) {
                    return new Promise((resolve) => setTimeout(resolve, ms));
                },

                playChime() {
                    try {
                        const AudioContext = window.AudioContext || window.webkitAudioContext;
                        if (!AudioContext) {
                            return;
                        }
                        const ctx = new AudioContext();
                        const oscillator = ctx.createOscillator();
                        const gain = ctx.createGain();
                        oscillator.type = 'sine';
                        oscillator.frequency.value = 740;
                        gain.gain.value = 0.12;
                        oscillator.connect(gain);
                        gain.connect(ctx.destination);
                        oscillator.start();
                        oscillator.stop(ctx.currentTime + 0.18);
                    } catch (error) {
                        console.warn('Unable to play chime.', error);
                    }
                },

                speakMessage(message) {
                    if (!('speechSynthesis' in window)) {
                        return;
                    }
                    const utterance = new SpeechSynthesisUtterance(message);
                    utterance.rate = 0.95;
                    utterance.pitch = 1;
                    window.speechSynthesis.cancel();
                    window.speechSynthesis.speak(utterance);
                },

                playThankYouSound() {
                    this.playChime();
                    this.speakMessage('Thank you! Please come again!');
                },

                applyStockDeduction() {
                    this.cart.forEach((item) => {
                        const itemId = Number(item.id);
                        const current = Number(this.productStock[itemId] ?? 0);
                        const qty = Number(item.qty || 0);
                        this.productStock[itemId] = Math.max(0, current - qty);
                    });
                },

                async refreshStock() {
                    try {
                        const response = await fetch(this.resolveUrl(this.routes.stock), {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });

                        if (!response.ok) {
                            return;
                        }

                        const data = await response.json();
                        if (data.status === 'success' && data.stocks) {
                            this.productStock = { ...this.productStock, ...data.stocks };
                        }
                    } catch (error) {
                        console.warn('Unable to refresh stock.', error);
                    }
                },

                async refreshPromotions() {
                    try {
                        const response = await fetch(this.resolveUrl(this.routes.promotions), {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });

                        if (!response.ok) {
                            return;
                        }

                        const data = await response.json();
                        if (data.status === 'success' && data.promotions) {
                            this.promotionIndex = data.promotions;
                            this.applyPromotionsToCart();
                        }
                    } catch (error) {
                        console.warn('Unable to refresh promotions.', error);
                    }
                },

                async saveOrder(type) {
                    if (this.isSaving) {
                        return;
                    }

                    this.isSaving = true;
                    this.mode = 'processing';
                    const requestStartedAt = Date.now();

                    try {
                        const response = await fetch(this.resolveUrl(this.routes.store), {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                            body: JSON.stringify({
                                cart_data: this.cart,
                                customer_id: document.getElementById('customer_id').value,
                                payment_type: type
                            })
                        });

                        const data = await response.json();
                        if (!response.ok || data.status !== 'success') {
                            throw new Error(data.message || 'Unable to complete payment.');
                        }

                        const elapsed = Date.now() - requestStartedAt;
                        if (elapsed < 700) {
                            await this.wait(700 - elapsed);
                        }

                        this.playThankYouSound();
                        this.applyStockDeduction();
                        this.refreshStock();
                        this.mode = 'success';
                        this.isSaving = false;

                        setTimeout(() => {
                            this.cart = [];
                            this.mode = 'cart';
                            this.timeLeft = 180;
                            this.cashReceived = null;
                            this.cashError = '';
                        }, 2000);
                    } catch (error) {
                        this.isSaving = false;
                        this.mode = 'cart';
                        alert('Error: ' + (error?.message || 'Unable to save order.'));
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94A3B8; }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .khqr-card {
            aspect-ratio: 20 / 29;
            border-radius: 1rem;
            background: #ffffff;
            box-shadow: 0 0 16px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: 1px solid #ebedf2;
            font-family: 'Nunito Sans', sans-serif;
        }
        .khqr-card-header {
            position: relative;
            height: 12%;
            background: #E1232E;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .khqr-wordmark {
            color: #ffffff;
            font-weight: 800;
            letter-spacing: 0.08em;
            font-size: clamp(12px, 2.8vw, 15px);
        }
        .khqr-header-cut {
            position: absolute;
            right: 0;
            bottom: -1px;
            border-left: 14px solid transparent;
            border-bottom: 14px solid #ffffff;
        }
        .khqr-card-body {
            padding: 7% 10% 6%;
        }
        .khqr-merchant-name {
            font-size: clamp(10px, 2.7vw, 12px);
            font-weight: 700;
            color: #374151;
            line-height: 1.2;
            margin-bottom: 0.2rem;
            text-align: left;
        }
        .khqr-amount-row {
            display: flex;
            align-items: baseline;
            gap: 0.45rem;
            color: #111827;
            text-align: left;
        }
        .khqr-amount-value {
            font-size: clamp(22px, 6.5vw, 30px);
            font-weight: 900;
            line-height: 1;
        }
        .khqr-currency {
            font-size: clamp(10px, 2.8vw, 12px);
            font-weight: 800;
            letter-spacing: 0.04em;
            color: #4b5563;
        }
        .khqr-separator {
            border-top: 1px dashed #d1d5db;
            margin: 0 0;
        }
        .khqr-qr-wrap {
            padding: 8% 10%;
            height: 55%;
        }
        .khqr-qr-box {
            position: relative;
            width: 100%;
            height: 100%;
        }
        .khqr-qr-center-icon {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: clamp(26px, 7vw, 34px);
            height: clamp(26px, 7vw, 34px);
            border-radius: 9999px;
            border: 2px solid #ffffff;
            background: #000000;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(13px, 3vw, 17px);
            font-weight: 900;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.25);
        }
    </style>
</x-app-layout>
