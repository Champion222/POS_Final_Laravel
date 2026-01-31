<x-app-layout>
    @section('header', auth()->user()->role === 'admin' ? 'Attendance Control' : 'My Time Clock')

    <div class="max-w-7xl mx-auto space-y-8">

        @if(auth()->user()->role === 'admin')
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-3xl p-5 border border-gray-100 shadow-sm flex items-center gap-4 relative overflow-hidden group hover:shadow-md transition-all duration-300">
                <div class="absolute right-0 top-0 opacity-5 transform translate-x-2 -translate-y-2 group-hover:scale-110 transition-transform">
                    <i class="fas fa-check-circle text-8xl text-emerald-500"></i>
                </div>
                <div class="h-14 w-14 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 text-2xl shadow-sm">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="relative z-10">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Present Today</p>
                    <h3 class="text-2xl font-black text-gray-800">{{ $stats['present'] }} <span class="text-sm text-gray-400 font-medium">/ {{ $stats['employees'] }}</span></h3>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-5 border border-gray-100 shadow-sm flex items-center gap-4 relative overflow-hidden group hover:shadow-md transition-all duration-300">
                <div class="absolute right-0 top-0 opacity-5 transform translate-x-2 -translate-y-2 group-hover:scale-110 transition-transform">
                    <i class="fas fa-clock text-8xl text-orange-500"></i>
                </div>
                <div class="h-14 w-14 rounded-2xl bg-orange-50 flex items-center justify-center text-orange-600 text-2xl shadow-sm">
                    <i class="fas fa-running"></i>
                </div>
                <div class="relative z-10">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Late Arrivals</p>
                    <h3 class="text-2xl font-black text-gray-800">{{ $stats['late'] }}</h3>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-5 border border-gray-100 shadow-sm flex items-center gap-4 relative overflow-hidden group hover:shadow-md transition-all duration-300">
                <div class="absolute right-0 top-0 opacity-5 transform translate-x-2 -translate-y-2 group-hover:scale-110 transition-transform">
                    <i class="fas fa-times-circle text-8xl text-red-500"></i>
                </div>
                <div class="h-14 w-14 rounded-2xl bg-red-50 flex items-center justify-center text-red-600 text-2xl shadow-sm">
                    <i class="fas fa-user-slash"></i>
                </div>
                <div class="relative z-10">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Absent</p>
                    <h3 class="text-2xl font-black text-gray-800">{{ $stats['absent'] }}</h3>
                </div>
            </div>

            <div class="bg-gradient-to-br from-indigo-600 to-blue-600 rounded-3xl p-5 text-white shadow-lg shadow-indigo-200 flex flex-col justify-center items-center text-center relative overflow-hidden group">
                <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <p class="text-indigo-100 text-xs font-bold uppercase tracking-widest relative z-10">{{ now()->format('l') }}</p>
                <h3 class="text-3xl font-black mt-1 relative z-10">{{ now()->format('M d') }}</h3>
                <div class="absolute -bottom-4 -right-4 text-white/10 text-6xl">
                    <i class="fas fa-calendar-day"></i>
                </div>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8 items-start">
            
            <div class="w-full lg:w-2/3 bg-white rounded-3xl shadow-xl shadow-gray-100/50 border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center bg-gray-50/30">
                    <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                        <i class="fas fa-list-ul text-indigo-500"></i> Daily Activity Log
                    </h3>
                    <div class="flex gap-2 items-center bg-white border border-gray-200 px-3 py-1.5 rounded-full shadow-sm">
                        <span class="flex h-2 w-2 relative">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wide">Live Feed</span>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50/50 text-gray-400 font-bold uppercase text-[10px] tracking-wider border-b border-gray-50">
                            <tr>
                                <th class="px-6 py-4">Employee</th>
                                <th class="px-6 py-4">In / Out Time</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($attendances as $record)
                            <tr class="group hover:bg-indigo-50/30 transition duration-200">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="relative">
                                            <img src="{{ $record->user->image ? asset('storage/'.$record->user->image) : 'https://ui-avatars.com/api/?name='.$record->user->name.'&background=random' }}" 
                                                 class="h-10 w-10 rounded-xl border border-gray-100 shadow-sm object-cover group-hover:scale-105 transition-transform duration-300">
                                            @if($record->checkout_time)
                                                <div class="absolute -bottom-1 -right-1 h-3 w-3 bg-gray-400 border-2 border-white rounded-full"></div>
                                            @else
                                                <div class="absolute -bottom-1 -right-1 h-3 w-3 bg-emerald-500 border-2 border-white rounded-full animate-pulse"></div>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-800 text-sm">{{ $record->user->name }}</p>
                                            <p class="text-[10px] font-bold text-indigo-400 bg-indigo-50 px-2 py-0.5 rounded-md w-max mt-0.5">{{ ucfirst($record->user->role) }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1 text-xs font-mono">
                                        <div class="flex items-center gap-2 text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg w-max">
                                            <i class="fas fa-sign-in-alt"></i> {{ \Carbon\Carbon::parse($record->checkin_time)->format('h:i A') }}
                                        </div>
                                        @if($record->checkout_time)
                                            <div class="flex items-center gap-2 text-red-500 bg-red-50 px-2 py-1 rounded-lg w-max">
                                                <i class="fas fa-sign-out-alt"></i> {{ \Carbon\Carbon::parse($record->checkout_time)->format('h:i A') }}
                                            </div>
                                        @else
                                            <div class="text-gray-300 text-[10px] pl-2 italic">-- Still Working --</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($record->status == 'late')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold uppercase bg-orange-100 text-orange-600 border border-orange-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span> Late
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-600 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> On Time
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if(!$record->checkout_time)
                                    <form action="{{ route('attendance.checkout', $record->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <button class="text-[10px] font-bold bg-white border border-red-200 text-red-500 hover:bg-red-500 hover:text-white hover:border-red-500 px-3 py-1.5 rounded-lg transition-all shadow-sm">
                                            FORCE OUT
                                        </button>
                                    </form>
                                    @else
                                        <span class="text-gray-300 text-xs"><i class="fas fa-check-double"></i> Done</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($attendances->hasPages())
                <div class="p-4 border-t border-gray-50 bg-gray-50/50">
                    {{ $attendances->links() }}
                </div>
                @endif
            </div>

            <div class="w-full lg:w-1/3">
                <div class="bg-white rounded-3xl shadow-xl shadow-indigo-100/50 border border-gray-100 p-8 sticky top-24">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="h-10 w-10 rounded-xl bg-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-200">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 text-lg">Manual Entry</h3>
                            <p class="text-xs text-gray-400">Record attendance for others.</p>
                        </div>
                    </div>

                    <form action="{{ route('attendance.store') }}" method="POST">
                        @csrf
                        <div class="space-y-5">
                            <div class="relative group">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Select Employee</label>
                                <div class="relative">
                                    <select name="employee_id" class="w-full pl-10 pr-4 py-3 rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition text-sm font-semibold appearance-none cursor-pointer">
                                        @foreach($employees as $emp) <option value="{{ $emp->id }}">{{ $emp->name }}</option> @endforeach
                                    </select>
                                    <i class="fas fa-user absolute left-4 top-4 text-gray-400 pointer-events-none"></i>
                                    <i class="fas fa-chevron-down absolute right-4 top-4 text-xs text-gray-400 pointer-events-none"></i>
                                </div>
                            </div>

                            <div class="relative group">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Check-in Time</label>
                                <div class="relative">
                                    <input type="time" name="check_in" value="{{ now()->format('H:i') }}" class="w-full pl-10 pr-4 py-3 rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition text-sm font-bold font-mono">
                                    <i class="fas fa-clock absolute left-4 top-4 text-gray-400 pointer-events-none"></i>
                                </div>
                            </div>

                            <div class="relative group">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Status</label>
                                <div class="grid grid-cols-3 gap-2">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="status" value="present" class="peer sr-only" checked>
                                        <div class="text-center py-2 rounded-lg border border-gray-200 text-xs font-bold text-gray-500 peer-checked:bg-emerald-500 peer-checked:text-white peer-checked:border-emerald-500 transition hover:bg-gray-50">Present</div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="status" value="late" class="peer sr-only">
                                        <div class="text-center py-2 rounded-lg border border-gray-200 text-xs font-bold text-gray-500 peer-checked:bg-orange-500 peer-checked:text-white peer-checked:border-orange-500 transition hover:bg-gray-50">Late</div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="status" value="absent" class="peer sr-only">
                                        <div class="text-center py-2 rounded-lg border border-gray-200 text-xs font-bold text-gray-500 peer-checked:bg-red-500 peer-checked:text-white peer-checked:border-red-500 transition hover:bg-gray-50">Absent</div>
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="w-full py-4 bg-gray-900 text-white rounded-xl font-bold text-sm shadow-lg shadow-gray-900/20 hover:bg-black hover:scale-[1.02] active:scale-95 transition-all duration-300 flex items-center justify-center gap-2">
                                <i class="fas fa-save"></i> Record Attendance
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @else
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-stretch">
            
            <div class="relative overflow-hidden rounded-[2.5rem] bg-[#0F172A] text-white shadow-2xl shadow-indigo-500/20 p-10 min-h-[450px] flex flex-col justify-between group">
                <div class="absolute top-0 right-0 -mr-20 -mt-20 h-96 w-96 rounded-full bg-indigo-600 opacity-20 blur-[100px] animate-pulse"></div>
                <div class="absolute bottom-0 left-0 -ml-20 -mb-20 h-96 w-96 rounded-full bg-purple-600 opacity-20 blur-[100px] animate-pulse" style="animation-delay: 2s;"></div>

                <div class="relative z-10 flex justify-between items-start">
                    <div>
                        <div class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1.5 text-[10px] font-bold backdrop-blur-md border border-white/10 mb-4 tracking-wide uppercase">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            {{ now()->format('l, F j, Y') }}
                        </div>
                        <h2 class="text-3xl font-bold">Hello, <br/><span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-cyan-400">{{ auth()->user()->name }}</span>.</h2>
                    </div>
                    <div class="h-12 w-12 rounded-2xl bg-white/10 backdrop-blur border border-white/10 flex items-center justify-center text-xl">
                        <i class="fas fa-fingerprint text-indigo-300"></i>
                    </div>
                </div>

                <div class="relative z-10 text-center my-8">
                    <div class="text-[6rem] leading-none font-black tracking-tighter text-white drop-shadow-2xl font-mono" id="live-clock">
                        {{ now()->format('H:i') }}
                    </div>
                    <p class="text-indigo-200 text-sm font-medium mt-2 tracking-widest uppercase">Bangkok Time (GMT+7)</p>
                </div>

                <div class="relative z-10 mt-auto">
                    <form action="{{ route('attendance.store') }}" method="POST">
                        @csrf
                        @if(auth()->user()->isCheckedIn)
                            <input type="hidden" name="type" value="clock_out">
                            <button type="submit" class="group relative w-full py-5 rounded-2xl bg-gradient-to-r from-red-500 to-pink-600 text-white font-bold text-lg shadow-xl shadow-red-500/30 hover:shadow-red-500/50 hover:scale-[1.02] active:scale-95 transition-all duration-300 overflow-hidden">
                                <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                                <span class="relative flex items-center justify-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                                        <i class="fas fa-stop text-sm"></i>
                                    </div>
                                    END SHIFT NOW
                                </span>
                            </button>
                            <p class="text-center mt-4 text-white/40 text-xs font-mono">
                                Started at: <span class="text-white font-bold">{{ \Carbon\Carbon::parse(auth()->user()->last_checkin_time)->format('h:i A') }}</span>
                            </p>
                        @else
                            <input type="hidden" name="type" value="clock_in">
                            <button type="submit" class="group relative w-full py-5 rounded-2xl bg-gradient-to-r from-indigo-500 to-cyan-500 text-white font-bold text-lg shadow-xl shadow-indigo-500/30 hover:shadow-indigo-500/50 hover:scale-[1.02] active:scale-95 transition-all duration-300 overflow-hidden">
                                <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                                <span class="relative flex items-center justify-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center animate-pulse">
                                        <i class="fas fa-play text-sm ml-0.5"></i>
                                    </div>
                                    START MY SHIFT
                                </span>
                            </button>
                            <p class="text-center mt-4 text-white/40 text-xs">Ready to start working?</p>
                        @endif
                    </form>
                </div>
            </div>

            <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/50 p-8 h-full flex flex-col">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-gray-800 text-xl flex items-center gap-2">
                        <i class="fas fa-history text-indigo-500"></i> Recent Activity
                    </h3>
                    <button class="text-xs font-bold text-gray-400 hover:text-indigo-600 bg-gray-50 px-3 py-1.5 rounded-lg transition">View All</button>
                </div>

                <div class="flex-1 overflow-y-auto custom-scrollbar space-y-3 pr-2">
                    @forelse($myHistory as $rec)
                    <div class="group flex items-center justify-between p-4 rounded-2xl bg-white border border-gray-100 hover:border-indigo-100 hover:shadow-md hover:translate-x-1 transition-all duration-300">
                        <div class="flex items-center gap-4">
                            <div class="h-12 w-12 rounded-xl flex items-center justify-center text-lg transition-colors duration-300 
                                {{ $rec->checkout_time ? 'bg-emerald-50 text-emerald-600 group-hover:bg-emerald-100' : 'bg-orange-50 text-orange-600 group-hover:bg-orange-100 animate-pulse' }}">
                                <i class="fas {{ $rec->checkout_time ? 'fa-check-circle' : 'fa-clock' }}"></i>
                            </div>
                            <div>
                                <p class="font-bold text-gray-800 text-sm">{{ $rec->created_at->format('D, M d') }}</p>
                                <div class="flex items-center gap-2 text-xs text-gray-400 mt-0.5 font-mono">
                                    <span class="text-gray-600">{{ \Carbon\Carbon::parse($rec->checkin_time)->format('H:i') }}</span>
                                    <i class="fas fa-arrow-right text-[8px] text-gray-300"></i>
                                    <span>{{ $rec->checkout_time ? \Carbon\Carbon::parse($rec->checkout_time)->format('H:i') : '...' }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-right">
                            <span class="inline-block px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wide 
                                {{ $rec->status == 'late' ? 'bg-red-50 text-red-600' : 'bg-gray-50 text-gray-500' }}">
                                {{ $rec->status }}
                            </span>
                            @if($rec->checkout_time)
                                <p class="text-[10px] text-gray-300 mt-1 font-mono">
                                    {{ \Carbon\Carbon::parse($rec->checkin_time)->diffInHours($rec->checkout_time) }}h worked
                                </p>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="flex flex-col items-center justify-center h-48 text-center text-gray-400">
                        <i class="fas fa-clipboard-list text-4xl mb-3 opacity-20"></i>
                        <p class="text-sm">No attendance records found.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <script>
            function updateClock() {
                const now = new Date();
                const timeString = new Intl.DateTimeFormat('en-GB', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false,
                    timeZone: 'Asia/Bangkok'
                }).format(now);
                
                const clockEl = document.getElementById('live-clock');
                if(clockEl) {
                    const [hours, minutes, seconds] = timeString.split(':');
                    clockEl.innerHTML = `${hours}:${minutes}<span class="animate-pulse text-indigo-400">:</span><span class="text-4xl text-gray-400 font-medium opacity-50">${seconds}</span>`;
                }
            }
            setInterval(updateClock, 1000);
            updateClock();
        </script>
        @endif

    </div>
</x-app-layout>
