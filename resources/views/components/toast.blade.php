@if (session()->has('success') || session()->has('error'))
<div x-data="{ show: true }" 
     x-show="show"
     x-init="setTimeout(() => show = false, 4000)"
     x-transition:enter="transform ease-out duration-300 transition"
     x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
     x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
     x-transition:leave="transition ease-in duration-100"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed top-24 right-5 z-50 flex w-full max-w-sm overflow-hidden bg-white/90 backdrop-blur-md rounded-2xl shadow-2xl border border-white/20 ring-1 ring-black/5">
    
    <div class="w-2 bg-gradient-to-b {{ session('error') ? 'from-red-500 to-pink-600' : 'from-emerald-400 to-teal-500' }}"></div>

    <div class="p-4 flex items-start gap-4 w-full">
        <div class="shrink-0">
            @if(session('error'))
                <div class="h-10 w-10 rounded-full bg-red-50 flex items-center justify-center text-red-500">
                    <i class="fas fa-times-circle text-xl"></i>
                </div>
            @else
                <div class="h-10 w-10 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-500">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
            @endif
        </div>

        <div class="flex-1 pt-0.5">
            <p class="text-sm font-extrabold text-gray-900">
                {{ session('error') ? 'Whoops!' : 'Success!' }}
            </p>
            <p class="text-xs font-medium text-gray-500 mt-1">
                {{ session('success') ?? session('error') }}
            </p>
        </div>

        <div class="shrink-0 flex pt-0.5">
            <button @click="show = false" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
</div>
@endif