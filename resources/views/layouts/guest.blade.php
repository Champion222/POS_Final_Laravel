<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'NEXPOX Mart') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body { font-family: 'Plus Jakarta Sans', sans-serif; }
            .glass-effect {
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.2);
            }
        </style>
    </head>
    <body class="antialiased text-slate-800 bg-white">
        
        <div class="min-h-screen flex w-full">
            
            <div class="hidden lg:flex w-1/2 bg-slate-900 relative overflow-hidden flex-col justify-between p-12 text-white">
                
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 to-slate-900 opacity-90"></div>
                <div class="absolute top-0 right-0 -mr-20 -mt-20 w-[600px] h-[600px] bg-indigo-500 rounded-full mix-blend-multiply filter blur-[100px] opacity-40 animate-pulse"></div>
                <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-[500px] h-[500px] bg-purple-600 rounded-full mix-blend-multiply filter blur-[100px] opacity-40 animate-pulse" style="animation-delay: 2s"></div>
                
                @php($brandLogo = 'https://i.postimg.cc/FHMsN52t/NEXPOS-Mart.png')
                <div class="relative z-10">
                    <div class="flex items-center gap-3">
                        <div class="h-12 w-12 rounded-full bg-gradient-to-br from-indigo-500 via-blue-500 to-purple-600 p-[2px] shadow-lg shadow-indigo-500/30">
                            <div class="h-full w-full rounded-full bg-slate-900 flex items-center justify-center overflow-hidden ring-1 ring-white/10">
                                <img src="{{ $brandLogo }}" alt="NEXPOX logo" class="h-full w-full rounded-full object-cover drop-shadow">
                            </div>
                        </div>
                        <div>
                            <span class="text-2xl font-black tracking-tight">NEXPOX</span>
                            <span class="block text-[10px] font-bold text-indigo-200 uppercase tracking-[0.28em]">Mart</span>
                        </div>
                    </div>
                </div>

                <div class="relative z-10 max-w-md">
                    <h2 class="text-5xl font-black tracking-tight leading-tight mb-6">Manage your business with confidence.</h2>
                    <p class="text-indigo-200 text-lg leading-relaxed">
                        Streamline your inventory, track sales in real-time, and manage your team efficiently with our enterprise-grade solution.
                    </p>
                    
                    <div class="mt-10 flex gap-4">
                        <div class="flex -space-x-4">
                            <img class="w-10 h-10 rounded-full border-2 border-slate-900" src="https://i.pravatar.cc/100?img=1" alt="">
                            <img class="w-10 h-10 rounded-full border-2 border-slate-900" src="https://i.pravatar.cc/100?img=2" alt="">
                            <img class="w-10 h-10 rounded-full border-2 border-slate-900" src="https://i.pravatar.cc/100?img=3" alt="">
                            <div class="w-10 h-10 rounded-full border-2 border-slate-900 bg-indigo-500 flex items-center justify-center text-xs font-bold text-white">
                                +2k
                            </div>
                        </div>
                        <div class="flex flex-col justify-center">
                            <div class="flex text-yellow-400 text-xs">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                            </div>
                            <span class="text-xs text-indigo-300 font-medium">Trusted by businesses globally</span>
                        </div>
                    </div>
                </div>

                <div class="relative z-10 flex justify-between items-center text-xs text-indigo-300 font-medium tracking-wide uppercase">
                    <span>© {{ date('Y') }} NEXPOX Mart</span>
                    <span>Enterprise V2.0</span>
                </div>
            </div>

            <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-white">
                <div class="w-full max-w-md space-y-8">
                    
                    <div class="lg:hidden text-center mb-8">
                        <div class="inline-flex items-center gap-2 justify-center">
                            <div class="h-11 w-11 rounded-full bg-gradient-to-br from-indigo-500 via-blue-500 to-purple-600 p-[2px] shadow-lg shadow-indigo-200">
                                <div class="h-full w-full rounded-full bg-white flex items-center justify-center overflow-hidden ring-1 ring-indigo-100">
                                    <img src="{{ $brandLogo }}" alt="NEXPOX logo" class="h-full w-full rounded-full object-cover">
                                </div>
                            </div>
                            <span class="text-2xl font-black text-slate-900 tracking-tight">NEXPOX</span>
                        </div>
                    </div>

                    <div class="bg-white">
                        {{ $slot }}
                    </div>

                    <div class="text-center">
                        <p class="text-sm text-slate-400">
                            Need help? <a href="#" class="font-bold text-indigo-600 hover:text-indigo-500 transition">Contact Support</a>
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </body>
</html>
