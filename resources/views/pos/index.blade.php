<x-app-layout>
    @section('header', 'POS Terminal')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="flex flex-col lg:flex-row h-[calc(100vh-7rem)] gap-6 p-1" x-data="posSystem()" x-cloak>
        
        <div class="w-full lg:w-2/3 flex flex-col gap-6 h-full">
            
            <div class="bg-white p-4 rounded-[2rem] shadow-xl shadow-gray-100/50 border border-gray-100 flex flex-col md:flex-row items-center gap-4 shrink-0 relative z-20">
                <div class="flex-1 relative w-full">
                    <i class="fas fa-search absolute left-5 top-4 text-indigo-300 text-lg"></i>
                    <input x-model="search" type="text" placeholder="Search products, barcodes..." 
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

            <div class="flex-1 overflow-y-auto custom-scrollbar pr-2 pb-4">
                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-5">
                    @forelse($products as $product)
                    <div x-show="matchesSearch('{{ strtolower($product->name) }}', '{{ $product->category_id }}', '{{ $product->barcode }}')"
                         @click.stop.prevent="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->sale_price }}, '{{ $product->image }}')"
                         :class="(productStock[{{ $product->id }}] ?? 0) <= 0 ? 'opacity-50 pointer-events-none' : ''"
                         class="group bg-white rounded-[2rem] p-4 border border-gray-100 shadow-sm hover:shadow-2xl hover:shadow-indigo-100/50 hover:-translate-y-1 transition-all duration-300 cursor-pointer flex flex-col h-full relative select-none overflow-hidden">
                        
                        <div class="h-40 bg-gray-50 rounded-[1.5rem] mb-4 overflow-hidden relative group-hover:bg-indigo-50 transition-colors">
                            @if($product->image) 
                                <img src="{{ asset('storage/'.$product->image) }}" loading="lazy" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                            @else 
                                <div class="w-full h-full flex items-center justify-center text-gray-300 group-hover:text-indigo-300 transition-colors">
                                    <i class="fas fa-box text-4xl"></i>
                                </div> 
                            @endif
                            
                            <div class="absolute top-3 left-3 px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider shadow-sm backdrop-blur-md border border-white/20"
                                 :class="(productStock[{{ $product->id }}] ?? 0) <= 5 ? 'bg-red-500/90 text-white' : 'bg-white/90 text-gray-800'">
                                <span x-text="(productStock[{{ $product->id }}] ?? {{ $product->qty }}) + ' Left'">{{ $product->qty }} Left</span>
                            </div>
                        </div>

                        <div class="flex-1 flex flex-col justify-between">
                            <div>
                                <h3 class="font-bold text-gray-800 text-sm leading-snug line-clamp-2 mb-1 group-hover:text-indigo-600 transition-colors">{{ $product->name }}</h3>
                                <p class="text-[10px] text-gray-400 font-mono mb-3">{{ $product->barcode }}</p>
                            </div>
                            
                            <div class="flex justify-between items-center mt-auto pt-3 border-t border-dashed border-gray-100">
                                <span class="text-indigo-600 font-black text-lg">${{ number_format($product->sale_price, 2) }}</span>
                                <div class="w-9 h-9 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-sm">
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

        <div class="w-full lg:w-1/3 flex flex-col h-full bg-white rounded-[2.5rem] shadow-2xl shadow-gray-200/50 border border-gray-100 overflow-hidden relative z-30">
            
            <div x-show="mode === 'cart'" class="flex flex-col h-full relative z-10" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0">
                
                <div class="p-6 border-b border-gray-50 flex justify-between items-center bg-white z-20">
                    <div>
                        <h2 class="font-black text-xl text-gray-900 tracking-tight">Current Order</h2>
                        <p class="text-xs text-gray-400 font-medium mt-0.5" x-text="cart.length + ' items added'"></p>
                    </div>
                    <button @click="clearCart()" x-show="cart.length > 0" class="text-xs font-bold text-red-500 bg-red-50 hover:bg-red-100 px-4 py-2 rounded-xl transition flex items-center gap-2">
                        <i class="fas fa-trash-alt"></i> Clear
                    </button>
                </div>

                <div class="px-6 pt-4 shrink-0">
                    <div class="relative">
                        <i class="fas fa-user-circle absolute left-4 top-3.5 text-indigo-300 text-lg"></i>
                        <select id="customer_id" class="w-full pl-12 pr-4 py-3 bg-gray-50 border-transparent rounded-xl text-sm font-bold text-gray-700 cursor-pointer focus:bg-white focus:ring-2 focus:ring-indigo-100 transition-all appearance-none">
                            <option value="">Guest Customer</option>
                            @foreach($customers as $customer) <option value="{{ $customer->id }}">{{ $customer->name }}</option> @endforeach
                        </select>
                        <i class="fas fa-chevron-down absolute right-4 top-4 text-gray-400 pointer-events-none text-xs"></i>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto p-6 space-y-4 custom-scrollbar" id="cart-scroll">
                    <template x-if="cart.length === 0">
                        <div class="h-full flex flex-col items-center justify-center text-center select-none">
                            <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-shopping-basket text-4xl text-gray-200"></i>
                            </div>
                            <h3 class="text-gray-900 font-bold text-lg">Your cart is empty</h3>
                            <p class="text-gray-400 text-sm mt-1 max-w-[200px]">Scan a barcode or click a product to add it here.</p>
                        </div>
                    </template>
                    
                    <template x-for="(item, index) in cart" :key="item.id">
                        <div class="flex items-center justify-between group p-3 rounded-2xl hover:bg-gray-50 border border-transparent hover:border-gray-100 transition-all">
                            <div class="flex items-center gap-4 overflow-hidden">
                                <div class="w-14 h-14 shrink-0 rounded-xl bg-gray-50 overflow-hidden border border-gray-100">
                                    <img :src="item.image ? '/storage/'+item.image : ''" class="w-full h-full object-cover">
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-bold text-sm text-gray-800 truncate" x-text="item.name"></h4>
                                    <div class="text-xs font-bold text-indigo-500 mt-1">$<span x-text="item.price.toFixed(2)"></span></div>
                                </div>
                            </div>
                            
                            <div class="flex items-center bg-white border border-gray-200 rounded-xl p-1 gap-2 shadow-sm">
                                <button @click.stop="updateQty(index, -1)" class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-400 hover:bg-red-50 hover:text-red-500 transition"><i class="fas fa-minus text-[10px]"></i></button>
                                <span class="w-4 text-center font-bold text-sm text-gray-900 select-none" x-text="item.qty"></span>
                                <button @click.stop="updateQty(index, 1)" class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-400 hover:bg-indigo-50 hover:text-indigo-600 transition"><i class="fas fa-plus text-[10px]"></i></button>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="p-6 bg-white border-t border-gray-50 shadow-[0_-10px_40px_rgba(0,0,0,0.03)] z-50 relative">
                    
                    <div class="space-y-2 mb-6">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-400 font-bold">Subtotal</span>
                            <span class="font-bold text-gray-800">$<span x-text="total.toFixed(2)"></span></span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-400 font-bold">Tax (0%)</span>
                            <span class="font-bold text-gray-800">$0.00</span>
                        </div>
                        <div class="h-px bg-gray-100 my-2"></div>
                        <div class="flex justify-between items-end">
                            <span class="text-gray-800 font-black text-lg">Total Amount</span>
                            <span class="text-4xl font-black text-indigo-600 tracking-tight">$<span x-text="total.toFixed(2)"></span></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <button type="button" @click="processPayment('cash')" class="group py-4 rounded-2xl bg-gray-100 text-gray-800 font-black text-sm transition-all flex items-center justify-center gap-2 hover:bg-gray-200 active:scale-95">
                            <i class="fas fa-money-bill-wave text-lg text-gray-400 group-hover:text-gray-600"></i> Cash
                        </button>
                        <button type="button" @click="processPayment('qr')" class="group py-4 rounded-2xl bg-indigo-600 text-white font-black text-sm transition-all flex items-center justify-center gap-2 hover:bg-indigo-700 shadow-xl shadow-indigo-200 active:scale-95">
                            <i class="fas fa-qrcode text-lg text-indigo-200 group-hover:text-white"></i> KHQR Pay
                        </button>
                    </div>
                </div>
            </div>

            <div x-show="mode === 'qr'" x-cloak 
                 class="flex flex-col h-full bg-gray-900 text-white absolute inset-0 z-50"
                 x-transition:enter="transition ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-full" 
                 x-transition:enter-end="opacity-100 translate-y-0">
                
                <div class="p-6 flex justify-between items-center border-b border-white/10">
                    <h2 class="font-bold text-lg flex items-center gap-2"><i class="fas fa-qrcode text-indigo-400"></i> Scan to Pay</h2>
                    <button @click="cancelQR()" class="text-xs font-bold bg-white/10 px-4 py-2 rounded-lg hover:bg-white/20 transition">Cancel</button>
                </div>

                <div class="flex-1 flex flex-col items-center justify-center p-8 text-center relative">
                    <p class="text-gray-400 text-sm font-bold uppercase tracking-widest mb-2">Total Amount</p>
                    <h1 class="text-6xl font-black mb-8 text-white tracking-tight">$<span x-text="total.toFixed(2)"></span></h1>
                    
                    <div class="bg-white p-2 rounded-3xl shadow-2xl shadow-indigo-500/20 w-72 h-72 flex items-center justify-center relative z-10 overflow-hidden">
                        <div x-show="loading" class="flex flex-col items-center text-gray-400 animate-pulse">
                            <i class="fas fa-circle-notch fa-spin text-4xl mb-3 text-indigo-500"></i>
                            <span class="text-xs font-bold tracking-widest">GENERATING QR...</span>
                        </div>
                        <div x-show="!loading" x-html="qrCode" class="w-full h-full p-2 [&>svg]:w-full [&>svg]:h-full"></div>
                        
                        <div x-show="!loading" class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white p-2 rounded-full shadow-lg border border-gray-100">
                            <img src="https://bakong.nbc.gov.kh/images/logo.svg" class="h-8 w-8">
                        </div>
                    </div>

                    <div class="mt-10 flex flex-col items-center gap-3">
                        <div class="text-3xl font-mono font-bold tracking-widest text-indigo-400" x-text="formatTime(timeLeft)"></div>
                        <div class="flex items-center gap-2 text-white/40 text-xs font-bold uppercase tracking-wide animate-pulse">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Waiting for payment...
                        </div>
                    </div>
                </div>
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
                    <button type="button" @click="confirmCashPayment()" class="py-3 rounded-2xl bg-emerald-600 text-white font-black text-sm hover:bg-emerald-700 transition">
                        Confirm Cash
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
                countdownTimer: null, timeLeft: 180, isPolling: false,
                cashModalOpen: false, cashReceived: null, cashError: '',
                productStock: @json($products->pluck('qty', 'id')),

                get totalCents() {
                    return this.cart.reduce((sum, item) => {
                        const priceCents = Math.round(Number(item.price || 0) * 100);
                        const qty = Number(item.qty || 0);
                        return sum + (priceCents * qty);
                    }, 0);
                },
                get total() { return this.totalCents / 100; },
                get changeDue() { return Number(this.cashReceived || 0) - this.total; },
                
                matchesSearch(name, cat, code) {
                    const s = this.search.toLowerCase();
                    return (name.includes(s) || (code && code.includes(s))) && (this.category === 'all' || this.category == cat);
                },
                addToCart(id, name, price, image) {
                    const itemId = Number(id);
                    const itemPrice = Number(price);
                    let index = this.cart.findIndex(i => Number(i.id) === itemId);
                    const available = Number(this.productStock[itemId] ?? 0);

                    if (available <= 0) {
                        alert('Out of stock.');
                        return;
                    }

                    if (index !== -1) {
                        const currentQty = Number(this.cart[index].qty || 0);
                        if (currentQty + 1 > available) {
                            alert(`Only ${available} left in stock.`);
                            return;
                        }
                        this.cart.splice(index, 1, { ...this.cart[index], qty: currentQty + 1 });
                        return;
                    }

                    this.cart.push({ id: itemId, name, price: itemPrice, qty: 1, image });
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
                            alert(`Only ${available} left in stock.`);
                            return;
                        }
                    }
                    if (newQty <= 0) {
                        this.cart.splice(index, 1);
                        return;
                    }

                    this.cart.splice(index, 1, { ...this.cart[index], qty: newQty });
                },
                clearCart() { if(confirm('Clear cart?')) this.cart = []; },
                
                processPayment(type) {
                    if (type === 'cash') {
                        this.openCashModal();
                        return;
                    }

                    if (this.cart.length === 0) { alert('Cart is empty!'); return; }
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

                    fetch('{{ route("pos.generate_qr") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify({ amount: amount.toFixed(2) })
                    })
                    .then(r => r.json())
                    .then(d => {
                        if (d.status !== 'success') {
                            this.cancelQR();
                            alert('Unable to generate QR. Please try again.');
                            return;
                        }
                        this.qrCode = d.qr_svg;
                        this.qrMd5 = d.md5;
                        this.loading = false;
                        this.isPolling = true; 
                        this.startPolling(); 
                    });
                },

                cancelQR() { 
                    this.isPolling = false; 
                    clearInterval(this.countdownTimer);
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
                            const res = await fetch('{{ route("pos.check_qr") }}', {
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
                        } catch (error) { console.log("Check failed, retrying..."); }
                        await new Promise(r => setTimeout(r, 1500));
                    }
                },

                formatTime(seconds) {
                    const m = Math.floor(seconds / 60).toString().padStart(2, '0');
                    const s = (seconds % 60).toString().padStart(2, '0');
                    return `${m}:${s}`;
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
                        const response = await fetch('{{ route("pos.products.stock") }}', {
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

                saveOrder(type) {
                    fetch('{{ route("pos.store") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify({
                            cart_data: this.cart,
                            customer_id: document.getElementById('customer_id').value,
                            payment_type: type
                        })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.status === 'success') {
                            this.applyStockDeduction();
                            this.refreshStock();
                            this.mode = 'success'; 
                            setTimeout(() => {
                                this.cart = []; 
                                this.mode = 'cart'; 
                                this.timeLeft = 180;
                                this.cashReceived = null;
                                this.cashError = '';
                            }, 2000);
                        } else {
                            alert('Error: ' + data.message);
                            this.mode = 'cart';
                        }
                    });
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
    </style>
</x-app-layout>
