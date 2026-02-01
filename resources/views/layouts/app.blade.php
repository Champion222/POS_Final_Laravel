<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'NEXPOX Mart') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #F8FAFC; }
        
        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #475569; }

        /* Sidebar Styles */
        .nav-item.active {
            background: linear-gradient(90deg, #6366F1 0%, #4F46E5 100%);
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
        }
        .nav-item:not(.active):hover {
            background: rgba(255, 255, 255, 0.05);
            color: white;
        }
    </style>
</head>
<body class="antialiased text-slate-800" x-data="{ sidebarOpen: false, notificationsOpen: false, logoutModal: false }">
    
    <div class="flex h-screen overflow-hidden">
        
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-72 bg-[#0f172a] text-white flex flex-col shadow-2xl transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto">
            
            <div class="h-24 flex items-center px-8 border-b border-gray-800/50">
                @php($brandLogo = 'https://i.postimg.cc/FHMsN52t/NEXPOS-Mart.png')
                <div class="flex items-center gap-4">
                    <div class="relative w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 via-blue-500 to-purple-600 p-[2px] shadow-lg shadow-indigo-500/30">
                        <div class="h-full w-full rounded-full bg-[#0f172a] flex items-center justify-center overflow-hidden ring-1 ring-white/10">
                            <img src="{{ $brandLogo }}" alt="NEXPOX logo" class="w-full h-full rounded-full object-cover">
                        </div>
                    </div>
                    <div>
                        <h1 class="text-2xl font-black tracking-tight text-white leading-none">NEXPOX</h1>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.28em] mt-1">Mart</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto custom-scrollbar">
                
                <a href="{{ route('dashboard') }}" class="nav-item flex items-center gap-3 px-4 py-3.5 rounded-2xl text-sm font-bold text-gray-400 transition-all duration-300 group {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-pie w-5 text-center"></i>
                    <span>Dashboard</span>
                </a>

                @if(in_array(auth()->user()->role, ['admin', 'cashier']))
                    <a href="{{ route('pos.index') }}" class="nav-item flex items-center gap-3 px-4 py-3.5 rounded-2xl text-sm font-bold text-gray-400 transition-all duration-300 group {{ request()->routeIs('pos.*') ? 'active' : '' }}">
                        <i class="fas fa-cash-register w-5 text-center"></i>
                        <span>POS Terminal</span>
                    </a>
                @endif

                @if(in_array(auth()->user()->role, ['admin', 'stock_manager']))
                    <div class="px-4 mt-8 mb-3">
                        <p class="text-[10px] font-extrabold text-gray-500 uppercase tracking-widest">Management</p>
                    </div>

                    <a href="{{ route('products.index') }}" class="nav-item flex items-center gap-3 px-4 py-3.5 rounded-2xl text-sm font-bold text-gray-400 transition-all duration-300 group {{ request()->routeIs('products.*') ? 'active' : '' }}">
                        <i class="fas fa-box-open w-5 text-center"></i>
                        <span>Inventory</span>
                    </a>

                    <a href="{{ route('categories.index') }}" class="nav-item flex items-center gap-3 px-4 py-3.5 rounded-2xl text-sm font-bold text-gray-400 transition-all duration-300 group {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                        <i class="fas fa-tags w-5 text-center"></i>
                        <span>Categories</span>
                    </a>
                @endif

                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('positions.index') }}" class="nav-item flex items-center gap-3 px-4 py-3.5 rounded-2xl text-sm font-bold text-gray-400 transition-all duration-300 group {{ request()->routeIs('positions.*') ? 'active' : '' }}">
                        <i class="fas fa-sitemap w-5 text-center"></i>
                        <span>Positions</span>
                    </a>
                    <a href="{{ route('employees.index') }}" class="nav-item flex items-center gap-3 px-4 py-3.5 rounded-2xl text-sm font-bold text-gray-400 transition-all duration-300 group {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                        <i class="fas fa-users w-5 text-center"></i>
                        <span>Staff & HR</span>
                    </a>
                @endif

                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('attendance.index') }}" class="nav-item flex items-center gap-3 px-4 py-3.5 rounded-2xl text-sm font-bold text-gray-400 transition-all duration-300 group {{ request()->routeIs('attendance.*') ? 'active' : '' }}">
                        <i class="fas fa-clock w-5 text-center"></i>
                        <span>Attendance</span>
                    </a>
                @endif

                @if(in_array(auth()->user()->role, ['admin', 'cashier']))
                    <a href="{{ route('reports.sales') }}" class="nav-item flex items-center gap-3 px-4 py-3.5 rounded-2xl text-sm font-bold text-gray-400 transition-all duration-300 group {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <i class="fas fa-file-invoice-dollar w-5 text-center"></i>
                        <span>Reports</span>
                    </a>
                @endif
            </nav>

            <div class="p-4 border-t border-gray-800/50 bg-[#0b1120]">
                <div class="flex items-center justify-between group p-2 rounded-xl transition-all hover:bg-white/5">
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3">
                        <div class="relative w-10 h-10 rounded-full bg-indigo-600 flex items-center justify-center text-sm font-bold border-2 border-[#1e293b] overflow-hidden">
                            <img src="{{ auth()->user()->image ? asset('storage/'.auth()->user()->image) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=4F46E5&color=fff' }}" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <p class="text-sm font-bold text-white">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] font-medium text-gray-500 capitalize">{{ str_replace('_', ' ', Auth::user()->role) }}</p>
                        </div>
                    </a>
                    
                    <button @click="logoutModal = true" class="w-9 h-9 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all" title="Sign Out">
                        <i class="fas fa-right-from-bracket text-sm"></i>
                    </button>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col h-full overflow-hidden relative bg-[#F8FAFC]">
            
            <header class="h-20 bg-white/80 backdrop-blur-xl border-b border-gray-200/60 flex items-center justify-between px-8 z-10 sticky top-0">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-indigo-600 transition">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h2 class="text-xl font-bold text-gray-800 tracking-tight">
                        @yield('header', 'Dashboard')
                    </h2>
                </div>
                
                <div class="flex items-center gap-6">
                    <div class="hidden md:flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-full shadow-sm">
                        <div class="p-1.5 bg-indigo-50 text-indigo-600 rounded-full">
                            <i class="fas fa-calendar-day text-xs"></i>
                        </div>
                        <span class="text-xs font-bold text-gray-600">{{ now()->format('l, M d, Y') }}</span>
                    </div>
                    
                    <div class="relative">
                        <button @click="notificationsOpen = true" class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:text-indigo-600 hover:border-indigo-200 shadow-sm transition-all duration-200 relative group">
                            <i class="fas fa-bell group-hover:scale-110 transition-transform"></i>
                            @if(isset($notifications) && count($notifications) > 0)
                                <span class="absolute top-0 right-0 flex h-3 w-3 -mt-1 -mr-1">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500 border-2 border-white"></span>
                                </span>
                            @endif
                        </button>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto p-8 scroll-smooth">
                {{ $slot }}
            </main>
        </div>

        <div class="relative z-50" aria-labelledby="slide-over-title" role="dialog" aria-modal="true" x-show="notificationsOpen" x-cloak>
            
            <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity" 
                 x-show="notificationsOpen"
                 x-transition:enter="ease-in-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in-out duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 @click="notificationsOpen = false"></div>
          
            <div class="fixed inset-0 overflow-hidden pointer-events-none">
                <div class="absolute inset-0 overflow-hidden">
                    <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                        <div class="pointer-events-auto w-screen max-w-md"
                             x-show="notificationsOpen"
                             x-transition:enter="transform transition ease-in-out duration-300"
                             x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                             x-transition:leave="transform transition ease-in-out duration-300"
                             x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">
                             
                            <div class="flex h-full flex-col bg-white shadow-2xl">
                                <div class="px-6 py-6 bg-gradient-to-r from-indigo-600 to-indigo-700">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <h2 class="text-lg font-bold text-white tracking-tight">Notifications</h2>
                                            <p class="mt-1 text-sm text-indigo-100 font-medium">System Alerts</p>
                                        </div>
                                        <button @click="notificationsOpen = false" class="text-indigo-200 hover:text-white transition">
                                            <i class="fas fa-times text-xl"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="flex-1 overflow-y-auto bg-slate-50 p-4">
                                    @if(isset($notifications) && count($notifications) > 0)
                                        <div class="space-y-3">
                                            @foreach($notifications as $item)
                                                <div class="flex items-center gap-4 bg-white p-4 rounded-2xl border border-red-100 shadow-sm hover:shadow-md transition-all duration-200">
                                                    
                                                    <div class="relative h-14 w-14 flex-none rounded-xl bg-slate-100 border border-slate-200 overflow-hidden">
                                                        @if($item->image)
                                                            <img class="h-full w-full object-cover" src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->name }}">
                                                        @else
                                                            <div class="flex h-full w-full items-center justify-center bg-indigo-50 text-indigo-300">
                                                                <i class="fas fa-image text-lg"></i>
                                                            </div>
                                                        @endif
                                                        
                                                        <div class="absolute top-0 right-0 -mt-1 -mr-1 h-5 w-5 rounded-full bg-red-500 text-white flex items-center justify-center shadow-sm ring-2 ring-white animate-pulse">
                                                            <i class="fas fa-exclamation text-[10px] font-bold"></i>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-slate-900 font-bold text-sm leading-tight line-clamp-1">{{ $item->name }}</p>
                                                        <div class="mt-1">
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-red-50 text-red-600 border border-red-100">
                                                                Low Stock: {{ $item->qty }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    
                                                    <a href="{{ route('products.edit', $item->id) }}" class="h-8 w-8 rounded-lg bg-slate-50 text-indigo-600 flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-colors border border-slate-200" title="Restock">
                                                        <i class="fas fa-arrow-right text-xs"></i>
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="h-full flex flex-col items-center justify-center text-gray-400">
                                            <i class="fas fa-bell-slash text-4xl mb-3 opacity-30"></i>
                                            <p class="text-sm">No new notifications</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="logoutModal" class="relative z-[100]" aria-labelledby="modal-title" role="dialog" aria-modal="true" x-cloak>
            <div x-show="logoutModal" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center">
                    <div x-show="logoutModal" 
                         @click.away="logoutModal = false"
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                         class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:w-full sm:max-w-sm p-6">
                        
                        <div class="text-center">
                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-red-50 mb-4 text-red-500">
                                <i class="fas fa-sign-out-alt text-xl"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Sign Out?</h3>
                            <p class="text-sm text-gray-500 mt-2">Are you sure you want to end your session?</p>
                        </div>
                        
                        <div class="mt-6 flex gap-3">
                            <button @click="logoutModal = false" class="w-full py-2.5 rounded-xl text-sm font-bold text-gray-700 bg-gray-100 hover:bg-gray-200 transition">
                                Cancel
                            </button>
                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <button type="submit" class="w-full py-2.5 rounded-xl text-sm font-bold text-white bg-red-600 hover:bg-red-700 shadow-lg shadow-red-200 transition">
                                    Confirm
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <x-toast />
</body>
</html>
