<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enroll - StockMaster</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .zen-glass {
            background: rgba(255, 255, 255, 0.4) !important;
            backdrop-filter: blur(40px) saturate(200%) !important;
            -webkit-backdrop-filter: blur(40px) saturate(200%) !important;
            border: 1px solid rgba(255, 255, 255, 0.5) !important;
        }
        .dark .zen-glass {
            background: rgba(8, 8, 8, 0.6) !important;
            backdrop-filter: blur(40px) saturate(200%) !important;
            -webkit-backdrop-filter: blur(40px) saturate(200%) !important;
            border: 1px solid rgba(255, 255, 255, 0.05) !important;
        }
        .squircle { border-radius: 2.5rem; }
        .mesh-bg {
            background-color: #f8fafc;
            background-image: 
                radial-gradient(at 0% 0%, rgba(8, 8, 8, 0.05) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(99, 102, 241, 0.05) 0px, transparent 50%);
        }
        .dark .mesh-bg {
            background-color: #080808;
            background-image: 
                radial-gradient(at 0% 0%, rgba(255, 255, 255, 0.03) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(99, 102, 241, 0.03) 0px, transparent 50%);
        }
    </style>
</head>
<body x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" 
      :class="{ 'dark': darkMode }"
      class="mesh-bg min-h-screen flex items-center justify-center p-6 transition-colors duration-700">
    
    <div class="max-w-md w-full space-y-12 animate-in fade-in zoom-in duration-1000">
        <!-- Logo Section -->
        <div class="text-center space-y-6">
            <div class="w-24 h-24 bg-onyx-950 dark:bg-white text-white dark:text-black squircle flex items-center justify-center mx-auto shadow-[0_30px_70px_rgba(0,0,0,0.3)] dark:shadow-[0_30px_70px_rgba(255,255,255,0.1)] group hover:scale-110 transition-transform duration-700 italic font-black text-3xl">
                S
            </div>
            <div>
                <h1 class="text-5xl font-black text-onyx-950 dark:text-white tracking-tighter italic uppercase leading-none">Enrollment</h1>
                <p class="text-onyx-400 font-black uppercase tracking-[0.4em] text-[9px] mt-3 italic">Request Enterprise Access</p>
            </div>
        </div>

        <!-- Register Card -->
        <div class="zen-glass squircle shadow-[0_50px_100px_-20px_rgba(0,0,0,0.3)] dark:shadow-[0_50px_100px_-20px_rgba(0,0,0,0.6)] overflow-hidden">
            <form action="{{ route('register') }}" method="POST" class="p-12 space-y-8">
                @csrf
                
                @if ($errors->any())
                <div class="p-5 bg-rose-500/10 border border-rose-500/20 rounded-2xl text-rose-500 text-[10px] font-black uppercase tracking-widest text-center italic">
                    {{ $errors->first() }}
                </div>
                @endif

                <div class="space-y-6">
                    <!-- Name -->
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-onyx-400 uppercase tracking-[0.4em] ml-2 italic">Full Identity</label>
                        <input type="text" name="name" :value="old('name')" required autofocus 
                            class="block w-full px-8 py-4 bg-black/5 dark:bg-white/5 border border-black/5 dark:border-white/5 rounded-2xl text-onyx-950 dark:text-white font-black text-sm uppercase tracking-tight focus:ring-0 focus:border-onyx-950 dark:focus:border-white transition-all outline-none" 
                            placeholder="OPERATOR_NAME">
                    </div>

                    <!-- Email -->
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-onyx-400 uppercase tracking-[0.4em] ml-2 italic">Contact Protocol (Email)</label>
                        <input type="email" name="email" :value="old('email')" required 
                            class="block w-full px-8 py-4 bg-black/5 dark:bg-white/5 border border-black/5 dark:border-white/5 rounded-2xl text-onyx-950 dark:text-white font-black text-sm uppercase tracking-tight focus:ring-0 focus:border-onyx-950 dark:focus:border-white transition-all outline-none" 
                            placeholder="ACCESS@ZENITH.CORP">
                    </div>

                    <!-- Password -->
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-onyx-400 uppercase tracking-[0.4em] ml-2 italic">Security Code</label>
                        <input type="password" name="password" required 
                            class="block w-full px-8 py-4 bg-black/5 dark:bg-white/5 border border-black/5 dark:border-white/5 rounded-2xl text-onyx-950 dark:text-white font-black text-sm uppercase tracking-tight focus:ring-0 focus:border-onyx-950 dark:focus:border-white transition-all outline-none" 
                            placeholder="••••••••">
                    </div>

                    <!-- Confirm Password -->
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-onyx-400 uppercase tracking-[0.4em] ml-2 italic">Verify Security Code</label>
                        <input type="password" name="password_confirmation" required 
                            class="block w-full px-8 py-4 bg-black/5 dark:bg-white/5 border border-black/5 dark:border-white/5 rounded-2xl text-onyx-950 dark:text-white font-black text-sm uppercase tracking-tight focus:ring-0 focus:border-onyx-950 dark:focus:border-white transition-all outline-none" 
                            placeholder="••••••••">
                    </div>
                </div>

                <button type="submit" class="w-full py-6 bg-onyx-950 dark:bg-white text-white dark:text-black rounded-2xl font-black uppercase tracking-[0.4em] text-[11px] italic shadow-2xl hover:scale-[1.02] active:scale-[0.98] transition-all mt-4">
                    Protocolize Access
                </button>
            </form>
        </div>

        <div class="text-center space-y-4">
            <p class="text-onyx-300 dark:text-onyx-700 text-[9px] font-black uppercase tracking-[0.5em] italic">
                Zenith Perfection V12.4 • Enrollment Node
            </p>
            <a href="{{ route('login') }}" class="inline-block text-[10px] font-black text-onyx-400 uppercase tracking-widest hover:text-onyx-950 dark:hover:text-white transition-all border-b border-onyx-400 hover:border-onyx-950 dark:hover:border-white pb-1 italic">
                Already Authorized? Return to Gate
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
</body>
</html>
