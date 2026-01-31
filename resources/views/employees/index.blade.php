<x-app-layout>
    @section('header', 'Staff Management')

    <div class="max-w-7xl mx-auto space-y-8">
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">
                <div class="flex items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-red-100 bg-red-50 px-5 py-4 text-sm font-semibold text-red-700">
                <div class="flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif
        
        <div class="flex flex-col md:flex-row justify-between items-end md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Team Directory</h1>
                <p class="text-gray-500 font-medium mt-1">Manage your employees, roles, and system access.</p>
            </div>
            
            <a href="{{ route('employees.create') }}" class="group flex items-center gap-2 bg-gray-900 hover:bg-black text-white px-6 py-3 rounded-2xl font-bold transition-all shadow-lg shadow-gray-200 hover:shadow-xl hover:-translate-y-0.5">
                <div class="bg-white/20 p-1 rounded-lg">
                    <i class="fas fa-plus text-xs"></i>
                </div>
                <span>Add Member</span>
            </a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Staff</p>
                <p class="text-2xl font-black text-gray-800 mt-1">{{ $employees->count() }}</p>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Admins</p>
                <p class="text-2xl font-black text-indigo-600 mt-1">
                    {{ $employees->where('position.target_role', 'admin')->count() }}
                </p>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Cashiers</p>
                <p class="text-2xl font-black text-blue-600 mt-1">
                    {{ $employees->where('position.target_role', 'cashier')->count() }}
                </p>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">System Users</p>
                <p class="text-2xl font-black text-emerald-600 mt-1">
                    {{ $employees->whereNotNull('user_id')->count() }}
                </p>
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-100/50 border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100 text-xs font-bold text-gray-400 uppercase tracking-wider">
                            <th class="px-8 py-6">Employee Profile</th>
                            <th class="px-6 py-6">Role & Position</th>
                            <th class="px-6 py-6">Contact Info</th>
                            <th class="px-6 py-6">System Access</th>
                            <th class="px-8 py-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($employees as $emp)
                        <tr class="group hover:bg-indigo-50/10 transition-colors duration-200">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="relative">
                                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-gray-600 font-bold text-lg shadow-inner group-hover:from-indigo-100 group-hover:to-blue-100 group-hover:text-indigo-600 transition-all duration-300">
                                            {{ substr($emp->name, 0, 1) }}
                                        </div>
                                        @if($emp->user_id)
                                            <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-white rounded-full" title="Active User"></div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900 text-base group-hover:text-indigo-600 transition-colors">{{ $emp->name }}</p>
                                        <p class="text-xs text-gray-400 font-medium">Joined {{ \Carbon\Carbon::parse($emp->start_date)->format('M Y') }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                @php
                                    $role = $emp->position->target_role;
                                    $colors = [
                                        'admin' => 'bg-purple-100 text-purple-700 border-purple-200',
                                        'stock_manager' => 'bg-orange-100 text-orange-700 border-orange-200',
                                        'cashier' => 'bg-blue-100 text-blue-700 border-blue-200',
                                        'employee' => 'bg-gray-100 text-gray-600 border-gray-200'
                                    ];
                                    $class = $colors[$role] ?? $colors['employee'];
                                @endphp
                                <span class="px-3 py-1.5 rounded-xl text-xs font-bold border {{ $class }} inline-flex items-center gap-1.5">
                                    @if($role == 'admin') <i class="fas fa-shield-alt"></i>
                                    @elseif($role == 'cashier') <i class="fas fa-cash-register"></i>
                                    @elseif($role == 'stock_manager') <i class="fas fa-box"></i>
                                    @else <i class="fas fa-user"></i>
                                    @endif
                                    {{ $emp->position->name }}
                                </span>
                            </td>
                            <td class="px-6 py-5">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2 text-gray-600">
                                        <i class="fas fa-envelope text-gray-300 text-xs w-4"></i>
                                        <span class="font-medium">{{ $emp->email }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-gray-500 text-xs">
                                        <i class="fas fa-phone text-gray-300 text-xs w-4"></i>
                                        <span>{{ $emp->phone }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                @if($emp->user_id)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Login Enabled
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-gray-50 text-gray-400 text-xs font-bold border border-gray-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                                        No Access
                                    </span>
                                @endif
                            </td>
                            <td class="px-8 py-5 text-right">
                                <div class="flex justify-end items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                    <a href="{{ route('employees.show', $emp->id) }}" 
                                       class="h-9 w-9 flex items-center justify-center rounded-xl bg-white border border-gray-200 text-gray-400 hover:text-indigo-600 hover:border-indigo-200 hover:shadow-sm transition-all" 
                                       title="View Details">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    
                                    <form action="{{ route('employees.destroy', $emp->id) }}" method="POST" onsubmit="return confirm('Permanently remove {{ $emp->name }}?');">
                                        @csrf @method('DELETE')
                                        <button class="h-9 w-9 flex items-center justify-center rounded-xl bg-white border border-gray-200 text-gray-400 hover:text-red-600 hover:border-red-200 hover:bg-red-50 transition-all" 
                                                title="Remove Employee">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="h-24 w-24 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-users text-4xl text-gray-200"></i>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900">No Employees Found</h3>
                                    <p class="text-gray-400 text-sm mt-1 mb-6 max-w-xs mx-auto">Start building your team by adding your first employee.</p>
                                    <a href="{{ route('employees.create') }}" class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-bold shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition">
                                        Add First Employee
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
