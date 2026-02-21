<x-app-layout>
    @section('header', 'Edit Employee')

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
                <div class="bg-gradient-to-br from-indigo-600 via-indigo-500 to-slate-900 rounded-[2rem] p-8 text-white shadow-2xl shadow-indigo-200 relative overflow-hidden">
                    <div class="relative z-10">
                        <div class="h-12 w-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm mb-4">
                            <i class="fas fa-user-pen text-xl"></i>
                        </div>
                        <h2 class="text-2xl font-bold">Update Staff Profile</h2>
                        <p class="text-indigo-100 text-sm mt-2 leading-relaxed">
                            Keep employee info accurate. You can update email, position, and set a new login password anytime.
                        </p>
                    </div>
                    <div class="absolute -right-6 -bottom-6 h-32 w-32 bg-white/10 rounded-full blur-2xl"></div>
                    <div class="absolute top-0 right-0 p-4 opacity-10">
                        <i class="fas fa-id-card text-8xl"></i>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm">
                    <div class="flex items-start gap-4">
                        <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                            <i class="fas fa-shield-check"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800 text-sm">Access Status</h4>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $employee->user_id ? 'Login is enabled for this employee.' : 'No system login is linked yet.' }}
                            </p>
                            @if($employee->user_id)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 mt-3 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Login Enabled
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 mt-3 rounded-lg bg-gray-50 text-gray-400 text-xs font-bold border border-gray-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                                    No Access
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <form action="{{ route('employees.update', $employee->id) }}" method="POST" class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100 relative">
                    @csrf
                    @method('PUT')

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
                                           value="{{ old('name', $employee->name) }}"
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
                                           value="{{ old('email', $employee->email) }}"
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
                                           value="{{ old('phone', $employee->phone) }}"
                                           class="w-full pl-11 pr-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 rounded-xl font-bold text-gray-800 transition-all placeholder-gray-300">
                                </div>
                            </div>

                            <div class="group">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Start Date</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-calendar-alt text-gray-300 group-focus-within:text-indigo-500 transition-colors"></i>
                                    </div>
                                    <input type="date" name="start_date" value="{{ old('start_date', $employee->start_date) }}" required
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
                                    <input type="radio" name="position_id" value="{{ $position->id }}" class="peer sr-only" required @checked(old('position_id', $employee->position_id) == $position->id)>
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

                    <div class="h-px bg-gray-100 my-8"></div>

                    <div class="mb-8 relative z-10">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2 mb-6">
                            <span class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-xs text-gray-500">3</span>
                            Login Password
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="group">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">New Password</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-key text-gray-300 group-focus-within:text-indigo-500 transition-colors"></i>
                                    </div>
                                    <input type="password" name="password" autocomplete="new-password" placeholder="Leave blank to keep current"
                                           class="w-full pl-11 pr-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 rounded-xl font-bold text-gray-800 transition-all placeholder-gray-300">
                                </div>
                            </div>

                            <div class="group">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Confirm Password</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-lock text-gray-300 group-focus-within:text-indigo-500 transition-colors"></i>
                                    </div>
                                    <input type="password" name="password_confirmation" autocomplete="new-password" placeholder="Repeat new password"
                                           class="w-full pl-11 pr-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 rounded-xl font-bold text-gray-800 transition-all placeholder-gray-300">
                                </div>
                            </div>
                        </div>

                        <p class="text-xs text-gray-400 font-medium mt-3">
                            Admin can update passwords for all system-access roles. This action appears in Activity History.
                        </p>
                    </div>

                    <div class="flex items-center justify-end gap-6 pt-4 border-t border-gray-100">
                        <a href="{{ route('employees.show', $employee->id) }}" class="text-gray-400 font-bold text-sm hover:text-gray-600 transition-colors">Cancel</a>
                        <button type="submit" class="bg-gray-900 text-white px-8 py-3.5 rounded-xl font-bold text-sm shadow-xl hover:bg-black hover:scale-[1.02] active:scale-95 transition-all duration-300 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed" @if($positions->isEmpty()) disabled @endif>
                            <span>Update Profile</span>
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
