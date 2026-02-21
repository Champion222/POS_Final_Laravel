<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - GenZPOS</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0f172a; }
        
        /* Smooth Fade In Animation */
        .fade-in { animation: fadeIn 0.6s ease-out forwards; opacity: 0; transform: translateY(10px); }
        @keyframes fadeIn { to { opacity: 1; transform: translateY(0); } }
        
        /* Custom Focus Ring for Inputs */
        .input-field:focus-within { box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15); border-color: #6366f1; background-color: #fff; }
    </style>
</head>
<body class="h-screen w-full flex items-center justify-center relative overflow-hidden">

    @php($brandLogo = 'https://i.postimg.cc/fTtdBdZf/Chat-GPT-Image-Feb-7-2026-03-27-39-PM.png')
    <div class="absolute top-[-10%] left-[-10%] w-[50vw] h-[50vw] rounded-full bg-indigo-600/20 blur-[120px] animate-pulse"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[50vw] h-[50vw] rounded-full bg-purple-600/20 blur-[120px] animate-pulse" style="animation-delay: 2s"></div>

    <div class="w-full max-w-6xl h-[70vh] bg-white rounded-[2.5rem] shadow-2xl flex overflow-hidden relative z-10 m-6 fade-in border border-gray-100/10">
        
        <div class="hidden lg:flex w-5/12 bg-[#0b1120] relative flex-col justify-between p-12 text-white overflow-hidden">
            
            <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-500 rounded-full mix-blend-overlay filter blur-[80px] opacity-20 transform translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-purple-500 rounded-full mix-blend-overlay filter blur-[80px] opacity-20 transform -translate-x-1/2 translate-y-1/2"></div>

                <div class="relative z-10 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 via-blue-500 to-purple-600 p-[2px] shadow-lg shadow-indigo-500/30">
                        <div class="h-full w-full rounded-full bg-[#0b1120] flex items-center justify-center overflow-hidden ring-1 ring-white/10">
                            <img src="{{ $brandLogo }}" alt="GenZPOS logo" class="w-full h-full rounded-full object-cover drop-shadow">
                        </div>
                    </div>
                    <div>
                        <h1 class="text-2xl font-black tracking-tight leading-none">GenZPOS</h1>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.28em] mt-1">MART</p>
                </div>
            </div>

            <div class="relative z-10">
                <h2 class="text-4xl font-black leading-tight mb-6">Manage your<br>business with <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">confidence.</span></h2>
                <p class="text-gray-400 text-sm leading-relaxed max-w-xs">
                    The all-in-one platform for inventory, sales, and staff management designed for modern enterprises.
                </p>
            </div>

            <div class="relative z-10 flex items-center gap-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> System Operational
            </div>
        </div>

        <div class="w-full lg:w-7/12 bg-white flex items-center justify-center p-8 lg:p-16 relative">
            <div class="w-full max-w-sm space-y-8">
                
                <div class="lg:hidden flex justify-center mb-6">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 via-blue-500 to-purple-600 p-[2px] shadow-lg shadow-indigo-200">
                        <div class="h-full w-full rounded-full bg-white flex items-center justify-center overflow-hidden ring-1 ring-indigo-100">
                            <img src="{{ $brandLogo }}" alt="GenZPOS logo" class="h-full w-full rounded-full object-cover">
                        </div>
                    </div>
                </div>

                <div class="text-center lg:text-left">
                    <h2 class="text-3xl font-extrabold text-gray-900">Welcome Back</h2>
                    <p class="text-gray-500 text-sm mt-2 font-medium">Please enter your credentials to access the dashboard.</p>
                </div>

                <form method="POST" action="{{ route('login', absolute: false) }}" class="space-y-5">
                    @csrf
                    
                    <div class="group">
                        <label class="block text-xs font-extrabold text-gray-400 uppercase tracking-wider mb-2 ml-1">Email Address</label>
                        <div class="relative input-field transition-all duration-200 rounded-xl bg-gray-50 border border-gray-100">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400 group-focus-within:text-indigo-500 transition-colors"></i>
                            </div>
                            <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="name@example.com" 
                                   class="block w-full pl-11 pr-4 py-3.5 bg-transparent border-none rounded-xl text-gray-900 placeholder-gray-400 focus:ring-0 sm:text-sm font-semibold">
                        </div>
                        @error('email') <p class="text-red-500 text-xs mt-1 ml-1 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div class="group">
                        <label class="block text-xs font-extrabold text-gray-400 uppercase tracking-wider mb-2 ml-1">Password</label>
                        <div class="relative input-field transition-all duration-200 rounded-xl bg-gray-50 border border-gray-100">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400 group-focus-within:text-indigo-500 transition-colors"></i>
                            </div>
                            <input type="password" name="password" required placeholder="••••••••" 
                                   class="block w-full pl-11 pr-4 py-3.5 bg-transparent border-none rounded-xl text-gray-900 placeholder-gray-400 focus:ring-0 sm:text-sm font-semibold">
                        </div>
                        @error('password') <p class="text-red-500 text-xs mt-1 ml-1 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 transition cursor-pointer">
                            <span class="text-xs font-bold text-gray-500 group-hover:text-gray-700 transition">Remember me</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">Forgot Password?</a>
                    </div>

                    <button type="submit" class="w-full bg-gray-900 text-white font-bold py-4 rounded-xl shadow-xl shadow-gray-900/10 hover:bg-indigo-600 hover:shadow-indigo-600/20 transform active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-2">
                        <span>Sign In</span>
                        <i class="fas fa-arrow-right text-sm opacity-70"></i>
                    </button>
                </form>

                <p class="text-center text-xs font-medium text-gray-400 mt-6">
                    Protected by reCAPTCHA and subject to the <a href="#" class="underline hover:text-gray-600">Privacy Policy</a>.
                </p>
            </div>
        </div>

    </div>

</body>
</html>


