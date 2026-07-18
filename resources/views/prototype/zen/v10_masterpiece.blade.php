<!DOCTYPE html>
<html lang="en" class="h-full" x-data="{ darkMode: true, sidebarOpen: true }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StockMaster V10: The Zen-Glass Masterpiece</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;300;400;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        onyx: { 950: '#030303', 900: '#0a0a0a', 800: '#141414', 700: '#1c1c1c' }
                    },
                    borderRadius: { 'squircle': '3rem' }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; background-color: #fcfcfc; }
        .dark body { background-color: #000000; }
        
        .masterpiece-glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(60px) saturate(200%);
            -webkit-backdrop-filter: blur(60px) saturate(200%);
            border: 0.5px solid rgba(0, 0, 0, 0.08);
            box-shadow: inset 0 1px 1px 0 rgba(255, 255, 255, 0.4), 0 20px 40px -10px rgba(0,0,0,0.05);
        }

        .dark .masterpiece-glass {
            background: rgba(10, 10, 10, 0.6);
            border: 0.5px solid rgba(255, 255, 255, 0.05);
            box-shadow: inset 0 1px 1px 0 rgba(255, 255, 255, 0.02), 0 30px 60px -15px rgba(0,0,0,0.6);
        }

        .micro-glow {
            position: relative;
        }
        .micro-glow::after {
            content: ''; position: absolute; inset: 0; border-radius: inherit;
            border: 1px solid transparent; background: linear-gradient(135deg, rgba(255,255,255,0.1), transparent) border-box;
            -webkit-mask: linear-gradient(#fff 0 0) padding-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor; mask-composite: exclude;
            opacity: 0.3; transition: opacity 0.5s;
        }
        .micro-glow:hover::after { opacity: 1; }

        @keyframes entrance {
            from { opacity: 0; transform: scale(0.98) translateY(10px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        .animate-entrance { animation: entrance 1s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; opacity: 0; }
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
        .delay-4 { animation-delay: 0.4s; }

        .ios-blob {
            position: fixed; width: 60vw; height: 60vw; border-radius: 50%;
            filter: blur(120px); z-index: -1; opacity: 0.05; pointer-events: none;
        }
        .blob-1 { top: -20%; right: -10%; background: #ffffff; }
        .dark .blob-1 { background: #333333; }
    </style>
</head>
<body class="h-full text-onyx-950 dark:text-white transition-colors duration-700 overflow-hidden">
    <div class="ios-blob blob-1"></div>

    <div class="flex h-full p-6 lg:p-10 gap-10">
        <!-- V10 Refined Floating Sidebar -->
        <aside 
            x-show="sidebarOpen"
            x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 -translate-x-10"
            x-transition:enter-end="opacity-100 translate-x-0"
            class="w-72 hidden lg:flex flex-col rounded-squircle masterpiece-glass relative z-50 p-10 space-y-12"
        >
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-2xl bg-onyx-950 dark:bg-white flex items-center justify-center shadow-2xl">
                    <i data-lucide="zap" class="w-5 h-5 text-white dark:text-black" stroke-width="2"></i>
                </div>
                <h1 class="text-xl font-black tracking-tighter uppercase italic leading-none">StockMaster</h1>
            </div>

            <nav class="flex-1 space-y-12">
                <div class="space-y-6">
                    <label class="text-[9px] lowercase tracking-[0.5em] font-light text-onyx-400 block px-4">Workspace</label>
                    <div class="space-y-2">
                        <a href="#" class="flex items-center gap-4 px-5 py-4 rounded-2xl bg-onyx-950 dark:bg-white text-white dark:text-black shadow-xl group">
                            <i data-lucide="layout-grid" class="w-4 h-4" stroke-width="1.5"></i>
                            <span class="text-xs font-bold tracking-tight">Intelligence</span>
                        </a>
                        <a href="#" class="flex items-center gap-4 px-5 py-4 rounded-2xl text-onyx-500 hover:bg-white/5 transition-all">
                            <i data-lucide="package" class="w-4 h-4" stroke-width="1.5"></i>
                            <span class="text-xs font-medium tracking-tight">Inventory Flow</span>
                        </a>
                        <a href="#" class="flex items-center gap-4 px-5 py-4 rounded-2xl text-onyx-500 hover:bg-white/5 transition-all">
                            <i data-lucide="pie-chart" class="w-4 h-4" stroke-width="1.5"></i>
                            <span class="text-xs font-medium tracking-tight">Audit Logs</span>
                        </a>
                    </div>
                </div>
            </nav>

            <div class="masterpiece-glass p-5 rounded-3xl flex items-center gap-4 border thin-border">
                <div class="w-10 h-10 rounded-full bg-onyx-800 border border-white/10 flex items-center justify-center font-bold text-xs text-white">AD</div>
                <div class="flex-1 min-w-0">
                    <p class="text-[10px] font-black truncate">Admin Prime</p>
                    <p class="text-[8px] lowercase tracking-widest text-onyx-500">v10.0 masterpiece</p>
                </div>
            </div>
        </aside>

        <!-- Main Masterpiece Content -->
        <main class="flex-1 flex flex-col gap-10 overflow-hidden">
            <!-- Precise Header -->
            <header class="flex items-center justify-between animate-entrance">
                <div>
                    <h2 class="text-4xl font-black tracking-tighter italic leading-none">Global Metrics</h2>
                    <p class="text-[11px] lowercase tracking-[0.4em] font-light text-onyx-500 mt-3">Refining proportions & industrial squircle architecture</p>
                </div>

                <div class="flex items-center gap-5">
                    <button @click="darkMode = !darkMode" class="w-14 h-14 rounded-3xl masterpiece-glass flex items-center justify-center hover:scale-110 active:scale-95 transition-all">
                        <i data-lucide="sun" x-show="darkMode" class="w-5 h-5"></i>
                        <i data-lucide="moon" x-show="!darkMode" class="w-5 h-5"></i>
                    </button>
                    <button class="px-10 h-14 rounded-3xl bg-onyx-950 dark:bg-white text-white dark:text-black font-black uppercase tracking-widest text-[10px] shadow-2xl hover:scale-105 active:scale-95 transition-all">
                        Execute Sync
                    </button>
                </div>
            </header>

            <!-- Perfected Bento Grid -->
            <div class="flex-1 overflow-y-auto custom-scrollbar pr-2">
                <div class="grid grid-cols-12 gap-8 h-full min-h-[900px]">
                    
                    <!-- Main Metric (Bento Focus) -->
                    <div class="col-span-12 lg:col-span-9 row-span-2 masterpiece-glass rounded-squircle p-14 flex flex-col justify-between micro-glow animate-entrance delay-1">
                        <div class="flex items-start justify-between">
                            <div>
                                <label class="text-[9px] lowercase tracking-[0.6em] font-light text-onyx-400 mb-4 block">liquidity threshold</label>
                                <h3 class="text-8xl font-black tracking-tighter italic leading-none">245.8<span class="text-3xl not-italic font-light opacity-20 ml-4">M</span></h3>
                            </div>
                            <div class="flex gap-3">
                                <div class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></div>
                                <span class="text-[10px] font-black uppercase tracking-widest text-emerald-500">stable flow</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-12 mt-12 pt-12 border-t border-white/5">
                            @foreach(['inflow volume' => '1.2M', 'reserve ratio' => '84%', 'safety margin' => '12d'] as $label => $val)
                            <div class="space-y-2">
                                <p class="text-[9px] lowercase tracking-[0.4em] font-light text-onyx-500 uppercase">{{ $label }}</p>
                                <p class="text-2xl font-black italic tracking-tight">{{ $val }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Right Column Bento -->
                    <div class="col-span-12 lg:col-span-3 row-span-1 masterpiece-glass rounded-squircle p-10 flex flex-col items-center justify-center text-center micro-glow animate-entrance delay-2">
                        <label class="text-[8px] lowercase tracking-[0.5em] font-light text-onyx-400 mb-4 block">item density</label>
                        <h4 class="text-4xl font-black tracking-tighter italic">8,540</h4>
                        <div class="mt-4 px-4 py-2 rounded-full bg-white/5 text-[8px] font-black tracking-widest uppercase">system optimal</div>
                    </div>

                    <div class="col-span-12 lg:col-span-3 row-span-1 masterpiece-glass rounded-squircle p-10 flex flex-col items-center justify-center text-center micro-glow animate-entrance delay-3">
                        <label class="text-[8px] lowercase tracking-[0.5em] font-light text-onyx-400 mb-4 block">operational</label>
                        <h4 class="text-4xl font-black tracking-tighter italic text-emerald-500">99.9<span class="text-lg opacity-40">%</span></h4>
                        <p class="text-[8px] font-light text-onyx-500 mt-2">Verified Architectural Node</p>
                    </div>

                    <!-- Lower Bento Grid -->
                    <div class="col-span-12 lg:col-span-4 row-span-2 masterpiece-glass rounded-squircle p-10 flex flex-col gap-8 animate-entrance delay-4">
                        <h4 class="text-lg font-black tracking-tighter italic pb-4 border-b border-white/5">Recent Intelligence</h4>
                        <div class="space-y-5 flex-1 overflow-hidden">
                            @foreach($recentTransactions as $tx)
                            <div class="flex items-center gap-5 group cursor-pointer">
                                <div class="w-10 h-10 rounded-2xl bg-white/5 flex items-center justify-center transition-all group-hover:bg-white group-hover:text-black">
                                    <i data-lucide="{{ $tx->type === 'in' ? 'arrow-down-left' : 'arrow-up-right' }}" class="w-4 h-4"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[10px] font-black tracking-tight truncate uppercase">{{ $tx->item->nama_barang }}</p>
                                    <p class="text-[8px] lowercase tracking-widest text-onyx-500 mt-1">node verified</p>
                                </div>
                                <span class="text-xs font-black italic">{{ $tx->type === 'in' ? '+' : '-' }}{{ $tx->quantity }}</span>
                            </div>
                            @endforeach
                        </div>
                        <button class="w-full py-5 rounded-2xl border border-white/5 text-[8px] lowercase tracking-[0.5em] font-light hover:bg-white/5 transition-all uppercase">
                            deep audit flow
                        </button>
                    </div>

                    <!-- Interactive Chart Space -->
                    <div class="col-span-12 lg:col-span-8 row-span-2 masterpiece-glass rounded-squircle p-10 relative overflow-hidden flex flex-col justify-between animate-entrance delay-3">
                        <div class="flex items-center justify-between mb-10">
                            <h4 class="text-lg font-black tracking-tighter italic">Predictive Waveform</h4>
                            <div class="flex gap-6">
                                @foreach(['q1', 'q2', 'q3', 'q4'] as $q)
                                <span class="text-[9px] font-black uppercase tracking-widest opacity-30 hover:opacity-100 cursor-pointer transition-opacity">{{ $q }}</span>
                                @endforeach
                            </div>
                        </div>
                        <div class="flex-1 flex items-end gap-3 px-10">
                            @for($i=0; $i<30; $i++)
                            <div class="flex-1 bg-white/5 rounded-full relative group h-full" style="height: {{ rand(20, 95) }}%">
                                <div class="absolute inset-0 bg-white/30 scale-y-0 group-hover:scale-y-100 transition-transform origin-bottom rounded-full shadow-[0_0_15px_rgba(255,255,255,0.2)]"></div>
                            </div>
                            @endfor
                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
