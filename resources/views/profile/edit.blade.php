<x-app-layout>
    @section('header', 'My Profile')

    <div class="max-w-5xl mx-auto">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-1 space-y-6">
                
                <div class="bg-white rounded-[2rem] p-8 shadow-xl shadow-indigo-100/50 border border-gray-100 text-center relative overflow-hidden group">
                    <div class="absolute top-0 left-0 w-full h-24 bg-gradient-to-br from-indigo-500 to-purple-600"></div>
                    
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="relative z-10 -mt-4">
                        @csrf @method('PATCH')
                        
                        <div class="relative inline-block group/avatar cursor-pointer">
                            <div class="w-32 h-32 rounded-full p-1 bg-white shadow-lg mx-auto overflow-hidden transition-transform duration-300 group-hover/avatar:scale-105">
                                <img id="preview" 
                                     src="{{ auth()->user()->image ? asset('storage/'.auth()->user()->image) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=random&color=fff' }}" 
                                     class="w-full h-full rounded-full object-cover border-4 border-gray-50">
                            </div>
                            
                            <div class="absolute inset-0 flex items-center justify-center rounded-full bg-black/40 opacity-0 group-hover/avatar:opacity-100 transition-opacity duration-300">
                                <i class="fas fa-camera text-white text-2xl drop-shadow-md"></i>
                            </div>
                            
                            <input type="file" name="image" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20" 
                                   onchange="document.getElementById('preview').src = window.URL.createObjectURL(this.files[0]); document.getElementById('save-photo-btn').classList.remove('hidden');">
                        </div>

                        <h3 class="font-bold text-gray-900 text-lg mt-4">{{ auth()->user()->name }}</h3>
                        <p class="text-xs text-gray-500 uppercase tracking-wider font-bold">{{ str_replace('_', ' ', auth()->user()->role) }}</p>

                        <button id="save-photo-btn" class="hidden mt-6 w-full py-2.5 rounded-xl bg-gray-900 text-white text-xs font-bold shadow-lg hover:bg-black transition-all animate-fade-in-up">
                            Save New Photo
                        </button>
                    </form>
                </div>

                <div class="bg-indigo-50/50 rounded-[2rem] p-6 border border-indigo-100">
                    <h4 class="font-bold text-indigo-900 text-sm mb-4 flex items-center gap-2">
                        <i class="fas fa-shield-alt"></i> Account Status
                    </h4>
                    <div class="space-y-3 text-xs font-medium text-indigo-800/70">
                        <div class="flex justify-between">
                            <span>Role Access</span>
                            <span class="bg-white px-2 py-0.5 rounded text-indigo-600 border border-indigo-100 capitalize">{{ str_replace('_', ' ', auth()->user()->role) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Joined On</span>
                            <span>{{ auth()->user()->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Last Updated</span>
                            <span>{{ auth()->user()->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-100/50 border border-gray-100 overflow-hidden">
                    
                    <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center bg-white">
                        <h3 class="font-bold text-gray-900 text-lg">Account Settings</h3>
                        <span class="text-xs font-bold text-gray-400 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100">
                            ID: #{{ auth()->user()->id }}
                        </span>
                    </div>

                    <div class="p-8">
                        <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
                            @csrf @method('PATCH')

                            <div class="group">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Full Name</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-user text-gray-300 group-focus-within:text-indigo-500 transition-colors"></i>
                                    </div>
                                    <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required
                                           class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border-transparent focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 rounded-xl font-bold text-gray-800 transition-all placeholder-gray-300">
                                </div>
                            </div>

                            <div class="group">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Email Address</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-envelope text-gray-300 group-focus-within:text-indigo-500 transition-colors"></i>
                                    </div>
                                    <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                                           class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border-transparent focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 rounded-xl font-bold text-gray-800 transition-all placeholder-gray-300">
                                </div>
                            </div>

                            <div class="pt-4 border-t border-gray-50">
                                <h4 class="font-bold text-gray-900 text-sm mb-4">Security</h4>
                                
                                <div class="group">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">New Password <span class="normal-case font-medium text-gray-300 ml-1">(Leave blank to keep current)</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="fas fa-lock text-gray-300 group-focus-within:text-indigo-500 transition-colors"></i>
                                        </div>
                                        <input type="password" name="password" autocomplete="new-password"
                                               class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border-transparent focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 rounded-xl font-bold text-gray-800 transition-all placeholder-gray-300">
                                    </div>
                                </div>

                                <div class="group mt-4">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Confirm Password</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="fas fa-check-circle text-gray-300 group-focus-within:text-indigo-500 transition-colors"></i>
                                        </div>
                                        <input type="password" name="password_confirmation" autocomplete="new-password"
                                               class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border-transparent focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 rounded-xl font-bold text-gray-800 transition-all placeholder-gray-300">
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-end pt-6">
                                <button type="submit" class="bg-indigo-600 text-white px-8 py-3.5 rounded-xl font-bold text-sm shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 flex items-center gap-2">
                                    <i class="fas fa-save"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <style>
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up { animation: fadeInUp 0.3s ease-out forwards; }
    </style>
</x-app-layout>
