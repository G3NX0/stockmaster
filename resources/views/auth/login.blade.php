<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Authorize | StockMaster Enterprise</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "primary": "#ffffff",
                        "background": "#050505",
                        "on-surface-variant": "#a3a3a3",
                    },
                    "borderRadius": {
                        "3xl": "2.5rem",
                    },
                    "fontFamily": {
                        "manrope": ["Manrope"],
                    },
                }
            }
        }
    </script>
    <style>
        @keyframes neural-drift {
            0% { transform: translate(0, 0) scale(1); opacity: 0.2; }
            50% { transform: translate(5%, 5%) scale(1.1); opacity: 0.4; }
            100% { transform: translate(0, 0) scale(1); opacity: 0.2; }
        }
        .neural-orb {
            position: fixed; border-radius: 50%; filter: blur(100px);
            z-index: 0; pointer-events: none;
            animation: neural-drift 15s infinite ease-in-out;
        }
        @keyframes light-sweep {
            0% { transform: translateX(-100%) skewX(-15deg); }
            100% { transform: translateX(200%) skewX(-15deg); }
        }
        .sweep-effect {
            position: fixed; inset: 0; z-index: 9999;
            pointer-events: none; opacity: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            animation: light-sweep 1s ease-in-out forwards;
            animation-delay: 0.5s;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .glass-card {
            background: rgba(10, 10, 10, 0.4);
            backdrop-filter: blur(40px);
            -webkit-backdrop-filter: blur(40px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        .input-glass {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
        }
        .input-glass:focus-within {
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(255, 255, 255, 0.2);
            box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.1);
        }
        .btn-primary {
            background: #ffffff;
            color: #000000;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-primary:hover {
            background: #f0f0f0;
            transform: translateY(-1px);
        }
        body {
            background-color: #010101;
            font-family: 'Manrope', sans-serif;
        }
    </style>
</head>
<body class="bg-background text-white selection:bg-white selection:text-black antialiased overflow-hidden">

<!-- Theme Sweep Effect -->
<div class="sweep-effect"></div>

<!-- Neural Pulse Background -->
<div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
    <div class="neural-orb w-[1000px] h-[1000px] bg-emerald-500/10 top-[-10%] left-[-10%]"></div>
    <div class="neural-orb w-[800px] h-[800px] bg-blue-500/10 bottom-[-5%] right-[-5%]" style="animation-delay: -5s"></div>
    <div class="neural-orb w-[600px] h-[600px] bg-rose-500/5 top-[40%] left-[20%]" style="animation-delay: -10s"></div>
    <div class="absolute inset-0 bg-gradient-to-tr from-black via-transparent to-transparent opacity-80"></div>
</div>

<!-- Simplified Header Navigation -->
<header class="fixed top-0 left-0 w-full z-[100] flex justify-between items-center px-8 md:px-16 py-10">
    <a href="{{ url('/') }}" class="flex items-center gap-3 group relative z-[110]">
        <div class="w-10 h-10 bg-white rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform shadow-xl">
            <span class="material-symbols-outlined text-black text-2xl">token</span>
        </div>
        <div class="font-bold text-2xl tracking-tighter text-white uppercase italic">StockMaster</div>
    </a>
    <a href="{{ url('/') }}" class="flex items-center gap-2 text-on-surface-variant hover:text-white transition-all duration-500 font-black text-[10px] uppercase tracking-[0.2em] active:scale-95 group relative z-[110]">
        <span>Return to Base</span>
        <span class="material-symbols-outlined group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform" style="font-size: 16px;">arrow_outward</span>
    </a>
</header>

<main class="relative z-10 flex min-h-screen items-center justify-center lg:justify-end px-6 md:px-16">
    <!-- Left Side: Dynamic Narrative -->
    <section class="hidden lg:flex flex-1 flex-col justify-center p-12 max-w-2xl animate-in fade-in slide-in-from-left-8 duration-1000">
        <div class="space-y-2 mb-8">
            <span class="text-[10px] font-black uppercase tracking-[0.5em] text-white/40 italic">System Protocol v12.4</span>
            <div class="w-12 h-1 bg-white/20 rounded-full"></div>
        </div>
        <h1 class="text-6xl font-extrabold text-white mb-8 drop-shadow-2xl leading-none italic uppercase tracking-tighter">
            Architecting the Future<br/>of Global Supply Chains
        </h1>
        <p class="text-on-surface-variant text-lg max-w-lg font-medium leading-relaxed opacity-80">
            Secure access to the Zenith Hub. Manage high-velocity inventory with precision engineering and predictive clarity.
        </p>
    </section>

    <!-- Right Side: Clean Login Form -->
    <section class="w-full max-w-[480px] animate-in fade-in slide-in-from-right-8 duration-1000">
        <div class="glass-card p-10 md:p-12 rounded-3xl space-y-10 border border-white/5">
            <!-- Mobile Branding -->
            <div class="lg:hidden mb-10 flex items-center gap-4">
                <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-black text-2xl">token</span>
                </div>
                <span class="font-extrabold text-3xl tracking-tighter text-white italic uppercase">StockMaster</span>
            </div>

            <div>
                <h2 class="text-4xl font-black text-white mb-3 italic uppercase tracking-tight">Initialize</h2>
                <p class="text-on-surface-variant font-medium text-sm tracking-tight opacity-70 italic">Identify personnel credentials for system access.</p>
            </div>

            @if ($errors->any())
                <div class="p-5 bg-rose-500/5 border border-rose-500/10 rounded-2xl text-rose-400 text-[10px] font-black uppercase tracking-widest text-center italic animate-pulse">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-8">
                @csrf
                <!-- Email Input -->
                <div class="space-y-3">
                    <label class="font-black text-[10px] uppercase tracking-[0.3em] text-on-surface-variant block ml-2 italic" for="email">Access Key (Email)</label>
                    <div class="relative group input-glass rounded-2xl overflow-hidden">
                        <input class="w-full bg-transparent border-none px-6 py-5 text-white placeholder:text-neutral-800 focus:ring-0 outline-none transition-all font-bold text-sm uppercase tracking-tight" 
                               id="email" name="email" value="{{ old('email') }}" required autofocus
                               placeholder="KEY_ID@ZENITH.CORP" type="email"/>
                    </div>
                </div>

                <!-- Password Input -->
                <div class="space-y-3">
                    <div class="flex justify-between items-center px-2">
                        <label class="font-black text-[10px] uppercase tracking-[0.3em] text-on-surface-variant italic" for="password">Security Protocol</label>
                        @if (Route::has('password.request'))
                            <a class="text-white font-black text-[9px] uppercase tracking-widest hover:opacity-70 transition-opacity" href="{{ route('password.request') }}">Recover</a>
                        @endif
                    </div>
                    <div class="relative group input-glass rounded-2xl overflow-hidden">
                        <input class="w-full bg-transparent border-none px-6 py-5 text-white placeholder:text-neutral-800 focus:ring-0 outline-none transition-all font-bold text-sm tracking-[0.3em]" 
                               id="password" name="password" required
                               placeholder="••••••••" type="password"/>
                        <button class="absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-white transition-colors" type="button" onclick="const p = document.getElementById('password'); p.type = p.type === 'password' ? 'text' : 'password'">
                            <span class="material-symbols-outlined">visibility</span>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center px-2">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="checkbox" name="remember" class="w-5 h-5 rounded-lg border-white/10 text-white focus:ring-0 bg-white/5 transition-all">
                        <span class="text-[10px] font-black text-on-surface-variant uppercase tracking-widest group-hover:text-white transition-all italic">Stay Authorized</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button class="w-full btn-primary font-black text-[11px] uppercase tracking-[0.4em] py-6 rounded-2xl active:scale-[0.98] transition-all flex items-center justify-center gap-3 shadow-2xl" type="submit">
                    <span class="italic">Initialize Access</span>
                </button>
            </form>

            <div class="pt-8 text-center">
                <p class="font-medium text-[10px] text-on-surface-variant uppercase tracking-widest">
                    New Personnel? 
                    @if (Route::has('register'))
                        <a class="text-white font-black hover:border-b border-white transition-all ml-1 italic" href="{{ route('register') }}">Enroll Here</a>
                    @else
                        <span class="text-white/40 italic ml-1 underline decoration-white/20">Contact Admin</span>
                    @endif
                </p>
            </div>
        </div>
    </section>
</main>

<footer class="fixed bottom-0 left-0 w-full flex flex-col md:flex-row justify-between items-center px-16 py-10 z-50 pointer-events-none opacity-40">
    <div class="font-black text-[9px] uppercase tracking-[0.4em] text-white pointer-events-auto italic">
        © 2026 StockMaster Enterprise.
    </div>
    <div class="flex gap-10 pointer-events-auto">
        <a class="font-black text-[9px] uppercase tracking-widest text-on-surface-variant hover:text-white transition-colors duration-300" href="#">Protocol</a>
        <a class="font-black text-[9px] uppercase tracking-widest text-on-surface-variant hover:text-white transition-colors duration-300" href="#">Network</a>
    </div>
</footer>

</body>
</html>
