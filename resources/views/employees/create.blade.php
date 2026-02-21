<x-app-layout>
    @section('header', 'Add New Employee')

    <div class="max-w-5xl mx-auto">
        @if (session('success'))
            <div class="mb-6 rounded-2xl border border-emerald-100 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">
                <div class="flex items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-2xl border border-red-100 bg-red-50 px-5 py-4 text-sm font-semibold text-red-700">
                <div class="flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-2xl border border-red-100 bg-white px-5 py-4 text-sm text-red-700 shadow-sm">
                <div class="flex items-start gap-3">
                    <i class="fas fa-times-circle mt-0.5 text-red-500"></i>
                    <div class="space-y-1">
                        <p class="font-bold">Please fix the following:</p>
                        <ul class="list-disc pl-5 text-xs text-red-600">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-indigo-600 rounded-[2rem] p-8 text-white shadow-xl shadow-indigo-200 relative overflow-hidden">
                    <div class="relative z-10">
                        <div class="h-12 w-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm mb-4">
                            <i class="fas fa-user-plus text-xl"></i>
                        </div>
                        <h2 class="text-2xl font-bold">New Staff Member</h2>
                        <p class="text-indigo-100 text-sm mt-2 leading-relaxed">
                            Create a profile for your new team member. Assigning specific roles like <strong>Cashier</strong> or <strong>Stock Manager</strong> will automatically generate system login credentials.
                        </p>
                    </div>
                    <div class="absolute -right-6 -bottom-6 h-32 w-32 bg-white/10 rounded-full blur-2xl"></div>
                    <div class="absolute top-0 right-0 p-4 opacity-10">
                        <i class="fas fa-id-card text-8xl"></i>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm">
                    <div class="flex items-start gap-4">
                        <div class="p-2 bg-yellow-50 text-yellow-600 rounded-lg">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800 text-sm">Pro Tip</h4>
                            <p class="text-xs text-gray-500 mt-1">
                                The default password for new system users is <code class="bg-gray-100 px-1 py-0.5 rounded text-gray-700 font-mono">genz@123</code>. Advise them to change it upon first login.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <form action="{{ route('employees.store') }}" method="POST" class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100 relative">
                    @csrf
                    
                    <div class="absolute top-0 right-0 p-8 opacity-5 pointer-events-none">
                        <i class="fas fa-pen-fancy text-9xl"></i>
                    </div>

                    <div class="mb-8 relative z-10">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2 mb-6">
                            <span class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-xs text-gray-500">1</span>
                            Personal Information
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="group">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Full Name</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-user text-gray-300 group-focus-within:text-indigo-500 transition-colors"></i>
                                    </div>
                                    <input type="text" name="name" required placeholder="John Doe"
                                           value="{{ old('name') }}"
                                           class="w-full pl-11 pr-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 rounded-xl font-bold text-gray-800 transition-all placeholder-gray-300">
                                </div>
                            </div>

                            <div class="group">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Email Address</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-envelope text-gray-300 group-focus-within:text-indigo-500 transition-colors"></i>
                                    </div>
                                    <input type="email" name="email" required placeholder="john@example.com"
                                           value="{{ old('email') }}"
                                           class="w-full pl-11 pr-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 rounded-xl font-bold text-gray-800 transition-all placeholder-gray-300">
                                </div>
                            </div>

                            <div class="group">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Phone Number</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-phone text-gray-300 group-focus-within:text-indigo-500 transition-colors"></i>
                                    </div>
                                    <input type="text" name="phone" required placeholder="+1 (555) 000-0000"
                                           value="{{ old('phone') }}"
                                           class="w-full pl-11 pr-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 rounded-xl font-bold text-gray-800 transition-all placeholder-gray-300">
                                </div>
                            </div>

                            <div class="group">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Start Date</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-calendar-alt text-gray-300 group-focus-within:text-indigo-500 transition-colors"></i>
                                    </div>
                                    <input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required
                                           class="w-full pl-11 pr-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 rounded-xl font-bold text-gray-800 transition-all cursor-pointer">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="h-px bg-gray-100 my-8"></div>

                    <div class="mb-8 relative z-10">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2 mb-6">
                            <span class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-xs text-gray-500">2</span>
                            Position & Access
                        </h3>

                        @if($positions->isEmpty())
                            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm font-semibold text-amber-700">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <span>Please create a position first to assign staff roles.</span>
                                </div>
                                <a href="{{ route('positions.index') }}" class="mt-3 inline-flex items-center gap-2 rounded-xl bg-gray-900 px-4 py-2 text-xs font-bold text-white hover:bg-black transition">
                                    <i class="fas fa-sitemap"></i>
                                    Go to Positions
                                </a>
                            </div>
                        @else
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($positions as $position)
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="position_id" value="{{ $position->id }}" class="peer sr-only" required @checked(old('position_id') == $position->id)>
                                    <div class="p-4 rounded-2xl border-2 border-gray-100 bg-white hover:border-indigo-100 hover:bg-gray-50 peer-checked:border-indigo-500 peer-checked:bg-indigo-50/50 transition-all duration-200 h-full flex flex-col justify-between">
                                        <div class="flex justify-between items-start mb-2">
                                            <div class="h-8 w-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center peer-checked:bg-indigo-500 peer-checked:text-white transition-colors">
                                                @if($position->target_role == 'admin') <i class="fas fa-shield-alt"></i>
                                                @elseif($position->target_role == 'cashier') <i class="fas fa-cash-register"></i>
                                                @elseif($position->target_role == 'stock_manager') <i class="fas fa-boxes"></i>
                                                @else <i class="fas fa-user-tag"></i>
                                                @endif
                                            </div>
                                            <div class="w-5 h-5 rounded-full border-2 border-gray-200 peer-checked:border-indigo-500 peer-checked:bg-indigo-500 flex items-center justify-center">
                                                <i class="fas fa-check text-white text-[10px] opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-800 text-sm">{{ $position->name }}</h4>
                                            <p class="text-xs text-gray-400 mt-1 capitalize">{{ str_replace('_', ' ', $position->target_role) }} Access</p>
                                        </div>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center justify-end gap-6 pt-4 border-t border-gray-100">
                        <a href="{{ route('employees.index') }}" class="text-gray-400 font-bold text-sm hover:text-gray-600 transition-colors">Cancel</a>
                        <button type="submit" class="bg-gray-900 text-white px-8 py-3.5 rounded-xl font-bold text-sm shadow-xl hover:bg-black hover:scale-[1.02] active:scale-95 transition-all duration-300 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed" @if($positions->isEmpty()) disabled @endif>
                            <span>Create Profile</span>
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</x-app-layout>
