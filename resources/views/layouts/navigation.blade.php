<nav x-data="{ open: false, scrolled: false, logoutModal: false }" 
     @scroll.window="scrolled = (window.pageYOffset > 20)"
     :class="scrolled ? 'bg-white/80 backdrop-blur-md shadow-sm' : 'bg-transparent'"
     class="fixed w-full top-0 z-50 transition-all duration-300 border-b border-gray-100/50">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20 items-center">
            
            @php($brandLogo = 'https://i.postimg.cc/FHMsN52t/NEXPOS-Mart.png')
            <div class="flex items-center gap-8">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                    <div class="relative w-12 h-12 rounded-2xl bg-transparent flex items-center justify-center overflow-hidden group-hover:scale-110 transition-transform duration-300">
                        <img src="{{ $brandLogo }}" alt="NEXPOX logo" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <span class="block text-lg font-black text-gray-900 leading-none tracking-tight group-hover:text-indigo-600 transition-colors">NEXPOX</span>
                        <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Mart</span>
                    </div>
                </a>

                <div class="hidden sm:flex space-x-1">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="fa-chart-pie">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @if(in_array(auth()->user()->role, ['admin', 'cashier']))
                        <x-nav-link :href="route('pos.index')" :active="request()->routeIs('pos.*')" icon="fa-cash-register">
                            {{ __('POS Terminal') }}
                        </x-nav-link>
                    @endif

                    @if(in_array(auth()->user()->role, ['admin', 'stock_manager']))
                        <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')" icon="fa-box">
                            {{ __('Inventory') }}
                        </x-nav-link>
                    @endif

                    <x-nav-link :href="route('attendance.index')" :active="request()->routeIs('attendance.*')" icon="fa-clock">
                        {{ __('Time Clock') }}
                    </x-nav-link>
                </div>
            </div>

            <div class="hidden sm:flex items-center gap-4">
                
                <button class="w-10 h-10 rounded-full bg-gray-50 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition flex items-center justify-center relative">
                    <i class="fas fa-bell"></i>
                    <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                </button>

                <div class="h-8 w-px bg-gray-200"></div>

                <div class="relative" x-data="{ dropdown: false }">
                    <button @click="dropdown = !dropdown" @click.away="dropdown = false" class="flex items-center gap-3 pl-2 pr-1 py-1 rounded-full hover:bg-gray-50 transition border border-transparent hover:border-gray-100 group">
                        <div class="text-right hidden md:block">
                            <p class="text-sm font-bold text-gray-800 leading-none group-hover:text-indigo-600 transition">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-0.5">{{ ucfirst(Auth::user()->role) }}</p>
                        </div>
                        <img src="{{ auth()->user()->image ? asset('storage/'.auth()->user()->image) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=6366f1&color=fff' }}" 
                             class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-sm group-hover:shadow-md transition">
                        <i class="fas fa-chevron-down text-xs text-gray-300 mr-2 group-hover:text-gray-500"></i>
                    </button>

                    <div x-show="dropdown" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                         class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden py-2 z-50 origin-top-right" style="display: none;">
                        
                        <div class="px-4 py-3 border-b border-gray-50 mb-1">
                            <p class="text-xs text-gray-400 font-bold uppercase">Account</p>
                            <p class="text-sm font-bold text-gray-800 truncate">{{ Auth::user()->email }}</p>
                        </div>

                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 transition">
                            <i class="fas fa-user-circle w-5 text-center"></i> Profile Settings
                        </a>
                        
                        <button @click="logoutModal = true; dropdown = false" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-red-500 hover:bg-red-50 transition text-left">
                            <i class="fas fa-sign-out-alt w-5 text-center"></i> Log Out
                        </button>
                    </div>
                </div>
            </div>

            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-xl text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition duration-150 ease-in-out">
                    <i class="fas fa-bars text-xl" :class="{'hidden': open, 'inline-flex': ! open }"></i>
                    <i class="fas fa-times text-xl" :class="{'hidden': ! open, 'inline-flex': open }"></i>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white border-t border-gray-100 absolute w-full shadow-lg">
        <div class="pt-2 pb-3 space-y-1 px-4">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            
            @if(in_array(auth()->user()->role, ['admin', 'cashier']))
                <x-responsive-nav-link :href="route('pos.index')" :active="request()->routeIs('pos.*')">
                    {{ __('POS Terminal') }}
                </x-responsive-nav-link>
            @endif

            <x-responsive-nav-link :href="route('attendance.index')" :active="request()->routeIs('attendance.*')">
                {{ __('Attendance') }}
            </x-responsive-nav-link>
        </div>

        <div class="pt-4 pb-1 border-t border-gray-100 bg-gray-50">
            <div class="px-4 flex items-center gap-3">
                <img src="{{ auth()->user()->image ? asset('storage/'.auth()->user()->image) : 'https://ui-avatars.com/api/?name='.auth()->user()->name }}" class="h-10 w-10 rounded-full border border-white shadow-sm">
                <div>
                    <div class="font-bold text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1 px-4 pb-4">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <button @click="logoutModal = true" class="w-full text-left flex items-center gap-2 px-4 py-2 text-base font-medium text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition">
                    <i class="fas fa-sign-out-alt"></i> {{ __('Log Out') }}
                </button>
            </div>
        </div>
    </div>

    <div x-show="logoutModal" class="relative z-[60]" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
        
        <div x-show="logoutModal" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                
                <div x-show="logoutModal" 
                     @click.away="logoutModal = false"
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-sm border border-gray-100">
                    
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-50 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fas fa-sign-out-alt text-red-500 text-lg"></i>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <h3 class="text-base font-bold leading-6 text-gray-900" id="modal-title">Ready to Leave?</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">Are you sure you want to log out of your account? Any unsaved changes in POS will be lost.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex w-full justify-center rounded-xl bg-red-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-red-500 sm:w-auto transition-colors">
                                Log Out
                            </button>
                        </form>
                        <button type="button" @click="logoutModal = false" class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-5 py-2.5 text-sm font-bold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</nav>

<div class="h-20"></div>
