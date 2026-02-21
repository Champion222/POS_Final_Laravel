<x-app-layout>
    @section('header', 'Structure & Roles')

    <div class="max-w-7xl mx-auto space-y-8">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gradient-to-br from-violet-600 to-indigo-700 rounded-3xl p-4 lg:p-5 min-h-[132px] text-white shadow-2xl shadow-indigo-200 relative overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
                <div class="relative z-10 flex flex-col justify-between h-full">
                    <div>
                        <p class="text-indigo-100 font-bold text-xs uppercase tracking-widest mb-2">Structure</p>
                        <h2 class="text-3xl lg:text-[2.4rem] font-black tracking-tight">{{ $totalPositions }}</h2>
                        <p class="text-indigo-200 text-sm mt-1.5 font-medium">Defined Department Roles</p>
                    </div>
                    <div class="mt-3 flex items-center gap-2 text-xs font-bold bg-white/10 w-max px-3 py-1.5 rounded-full border border-white/10 backdrop-blur-md">
                        <i class="fas fa-check-circle text-emerald-400"></i> System Active
                    </div>
                </div>
                <div class="absolute right-0 top-0 opacity-10 transform translate-x-10 -translate-y-10">
                    <i class="fas fa-sitemap text-7xl"></i>
                </div>
                <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/10 rounded-full blur-3xl"></div>
            </div>

            <div class="bg-white rounded-3xl p-4 lg:p-5 min-h-[132px] border border-gray-100 shadow-xl shadow-gray-100/50 relative overflow-hidden group hover:border-indigo-50 transition-all duration-300">
                <div class="relative z-10">
                    <div class="flex justify-between items-start mb-3">
                        <div class="p-2 bg-orange-50 text-orange-500 rounded-xl lg:rounded-2xl group-hover:bg-orange-500 group-hover:text-white transition-colors duration-300">
                            <i class="fas fa-users text-base"></i>
                        </div>
                        <span class="bg-gray-50 text-gray-400 text-[10px] font-bold px-2 py-1 rounded-lg uppercase">Global</span>
                    </div>
                    <h2 class="text-[2rem] lg:text-4xl font-extrabold text-gray-900">{{ $totalStaff }}</h2>
                    <p class="text-sm text-gray-500 font-medium mt-1">Active Staff Members</p>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-4 lg:p-5 min-h-[132px] border border-gray-100 shadow-xl shadow-gray-100/50 relative overflow-hidden group hover:border-emerald-50 transition-all duration-300">
                <div class="relative z-10">
                    <div class="flex justify-between items-start mb-3">
                        <div class="p-2 bg-emerald-50 text-emerald-600 rounded-xl lg:rounded-2xl group-hover:bg-emerald-600 group-hover:text-white transition-colors duration-300">
                            <i class="fas fa-shield-alt text-base"></i>
                        </div>
                        <span class="bg-emerald-50 text-emerald-600 text-[10px] font-bold px-2 py-1 rounded-lg uppercase">Secured</span>
                    </div>
                    <h2 class="text-[2rem] lg:text-4xl font-extrabold text-gray-900">{{ $systemRoles }}</h2>
                    <p class="text-sm text-gray-500 font-medium mt-1">Roles with System Access</p>
                </div>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8 items-start">
            
            <div class="w-full lg:w-2/3 bg-white rounded-[2.5rem] shadow-xl shadow-gray-100/50 border border-gray-100 overflow-hidden">
                
                <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center bg-white">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-1 rounded-full bg-indigo-500"></div>
                        <h3 class="font-bold text-gray-900 text-xl">Organization Roles</h3>
                    </div>
                    <div class="bg-gray-50 px-3 py-1.5 rounded-lg text-xs font-bold text-gray-400 uppercase tracking-wide">
                        <i class="fas fa-list-ul mr-1"></i> Directory
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100 text-xs font-bold text-gray-400 uppercase tracking-wider">
                                <th class="px-8 py-5">Role Title</th>
                                <th class="px-6 py-5">Base Salary</th>
                                <th class="px-6 py-5">System Permission</th>
                                <th class="px-6 py-5 text-center">Staff Count</th>
                                <th class="px-6 py-5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($positions as $pos)
                            <tr class="group hover:bg-indigo-50/10 transition-colors duration-200">
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-4">
                                        <div class="h-12 w-12 rounded-2xl flex items-center justify-center text-xl shadow-sm border transition-transform group-hover:scale-110 duration-300
                                            {{ $pos->target_role == 'admin' ? 'bg-purple-100 text-purple-600 border-purple-200' : '' }}
                                            {{ $pos->target_role == 'stock_manager' ? 'bg-orange-100 text-orange-600 border-orange-200' : '' }}
                                            {{ $pos->target_role == 'cashier' ? 'bg-blue-100 text-blue-600 border-blue-200' : '' }}
                                            {{ $pos->target_role == 'employee' ? 'bg-gray-100 text-gray-500 border-gray-200' : '' }}
                                        ">
                                            @if($pos->target_role == 'admin') <i class="fas fa-crown"></i>
                                            @elseif($pos->target_role == 'stock_manager') <i class="fas fa-boxes-stacked"></i>
                                            @elseif($pos->target_role == 'cashier') <i class="fas fa-cash-register"></i>
                                            @else <i class="fas fa-user-tag"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-800 text-base group-hover:text-indigo-600 transition-colors">{{ $pos->name }}</p>
                                            <p class="text-[10px] text-gray-400 font-mono mt-0.5">ID: #{{ $pos->id }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="font-mono font-bold text-gray-700 bg-gray-50 px-3 py-1.5 rounded-lg w-max border border-gray-100">
                                        ${{ number_format($pos->base_salary, 2) }}
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    @if($pos->target_role === 'admin')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-purple-50 text-purple-700 border border-purple-100">
                                            <span class="relative flex h-2 w-2">
                                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-purple-400 opacity-75"></span>
                                              <span class="relative inline-flex rounded-full h-2 w-2 bg-purple-500"></span>
                                            </span>
                                            Full Admin
                                        </span>
                                    @elseif($pos->target_role === 'stock_manager')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-orange-50 text-orange-700 border border-orange-100">
                                            <i class="fas fa-box text-[10px]"></i> Inventory
                                        </span>
                                    @elseif($pos->target_role === 'cashier')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                            <i class="fas fa-desktop text-[10px]"></i> POS Access
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-gray-100 text-gray-500 border border-gray-200">
                                            <i class="fas fa-lock text-[10px]"></i> No Access
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-5 text-center">
                                    @if($pos->employees_count > 0)
                                        <div class="inline-flex items-center justify-center h-8 min-w-[2rem] px-2 rounded-lg bg-gray-900 text-white font-bold text-xs shadow-md shadow-gray-200">
                                            {{ $pos->employees_count }}
                                        </div>
                                    @else
                                        <span class="text-gray-300 text-xs italic font-medium">Empty</span>
                                    @endif
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <div class="flex justify-end items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                        @if($pos->employees_count > 0)
                                            <button class="h-9 w-9 rounded-xl bg-gray-50 border border-gray-200 text-gray-300 cursor-not-allowed flex items-center justify-center" title="Cannot delete: Active staff assigned">
                                                <i class="fas fa-lock text-xs"></i>
                                            </button>
                                        @else
                                            <form action="{{ route('positions.destroy', $pos->id) }}" method="POST" onsubmit="return confirm('Delete this position?');">
                                                @csrf @method('DELETE')
                                                <button class="h-9 w-9 rounded-xl bg-white border border-gray-200 text-gray-400 hover:text-red-600 hover:border-red-200 hover:bg-red-50 flex items-center justify-center transition-all shadow-sm" title="Delete Position">
                                                    <i class="fas fa-trash-alt text-xs"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-8 py-16 text-center text-gray-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-folder-open text-4xl mb-3 opacity-20"></i>
                                        <p class="font-medium">No positions defined yet.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="w-full lg:w-1/3 sticky top-24">
                <div class="bg-gray-900 rounded-[2.5rem] shadow-2xl shadow-gray-900/20 p-8 relative overflow-hidden text-white">
                    <div class="absolute top-0 right-0 -mr-10 -mt-10 h-40 w-40 bg-indigo-600 rounded-full blur-[60px] opacity-30"></div>
                    <div class="absolute bottom-0 left-0 -ml-10 -mb-10 h-40 w-40 bg-purple-600 rounded-full blur-[60px] opacity-30"></div>

                    <div class="relative z-10">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="h-12 w-12 bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/10 shadow-lg">
                                <i class="fas fa-plus text-indigo-300 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-xl">Create Position</h3>
                                <p class="text-xs text-gray-400 font-medium">Define a new role for your team.</p>
                            </div>
                        </div>

                        <form action="{{ route('positions.store') }}" method="POST">
                            @csrf
                            <div class="space-y-5">
                                
                                <div class="group">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Position Title</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="fas fa-tag text-gray-500 group-focus-within:text-indigo-400 transition-colors"></i>
                                        </div>
                                        <input type="text" name="name" required placeholder="e.g. Stock Supervisor" 
                                               class="w-full pl-11 pr-4 py-4 bg-white/5 border border-white/10 rounded-xl text-sm font-bold text-white placeholder-gray-600 focus:bg-white/10 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all outline-none">
                                    </div>
                                </div>

                                <div class="group">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Base Salary</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="fas fa-dollar-sign text-gray-500 group-focus-within:text-green-400 transition-colors"></i>
                                        </div>
                                        <input type="number" step="0.01" name="base_salary" required placeholder="0.00" 
                                               class="w-full pl-11 pr-4 py-4 bg-white/5 border border-white/10 rounded-xl text-sm font-bold text-white placeholder-gray-600 focus:bg-white/10 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all outline-none font-mono">
                                    </div>
                                </div>

                                <div class="group">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">System Access</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="fas fa-key text-gray-500 group-focus-within:text-purple-400 transition-colors"></i>
                                        </div>
                                        <select name="target_role" class="w-full pl-11 pr-10 py-4 bg-white/5 border border-white/10 rounded-xl text-sm font-bold text-gray-200 focus:bg-white/10 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all outline-none appearance-none cursor-pointer">
                                            <option value="employee" class="bg-gray-900 text-gray-400">No Login (Basic Staff)</option>
                                            <option value="cashier" class="bg-gray-900 text-white">POS Only (Cashier)</option>
                                            <option value="stock_manager" class="bg-gray-900 text-white">Stock Manager</option>
                                            <option value="admin" class="bg-gray-900 text-purple-300">Full Admin Access</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                            <i class="fas fa-chevron-down text-xs text-gray-500"></i>
                                        </div>
                                    </div>
                                    <p class="text-[10px] text-gray-500 mt-2 px-1 flex items-center gap-1.5">
                                        <i class="fas fa-info-circle text-indigo-400"></i> Determines which dashboard they see.
                                    </p>
                                </div>

                                <button type="submit" class="group w-full py-4 bg-white text-gray-900 rounded-xl font-bold text-sm shadow-lg shadow-white/5 hover:bg-indigo-50 transition-all duration-300 flex items-center justify-center gap-2 transform active:scale-95 mt-4">
                                    <span>Save Position</span>
                                    <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
