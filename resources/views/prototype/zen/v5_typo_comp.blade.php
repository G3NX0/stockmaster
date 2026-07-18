<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zen-Glass V5: Typography & Components</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;300;400;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #f8fafc;
            -webkit-font-smoothing: antialiased;
        }
        .thin-border {
            border: 0.5px solid rgba(0, 0, 0, 0.08);
        }
    </style>
</head>
<body class="h-full text-slate-950 transition-colors duration-500">
    <div class="flex h-full">
        <!-- Sidebar V5 (Combined) -->
        <aside class="w-80 bg-white border-r thin-border flex flex-col relative z-50">
            <div class="p-10">
                <div class="flex items-center gap-4">
                    <div class="w-8 h-8 rounded-full bg-slate-950 flex items-center justify-center">
                        <i data-lucide="package" class="w-4 h-4 text-white" stroke-width="1.5"></i>
                    </div>
                    <h1 class="text-xl font-black tracking-tighter uppercase italic">StockMaster V5</h1>
                </div>
            </div>

            <nav class="flex-1 px-8 space-y-12">
                <div class="space-y-4">
                    <label class="text-[10px] lowercase tracking-[0.4em] font-light text-slate-400 block px-2">Navigation</label>
                    <div class="space-y-1">
                        <a href="#" class="flex items-center gap-4 px-4 py-4 rounded-2xl bg-slate-950 text-white shadow-xl shadow-slate-900/20">
                            <i data-lucide="layout-grid" class="w-4 h-4" stroke-width="1.5"></i>
                            <span class="text-sm font-bold tracking-tight">Overview</span>
                        </a>
                        <a href="#" class="flex items-center gap-4 px-4 py-4 rounded-2xl text-slate-400 hover:bg-slate-50">
                            <i data-lucide="package" class="w-4 h-4" stroke-width="1.5"></i>
                            <span class="text-sm font-medium tracking-tight">Inventory</span>
                        </a>
                    </div>
                </div>

                <div class="space-y-4">
                    <label class="text-[10px] lowercase tracking-[0.4em] font-light text-slate-400 block px-2">Intelligence</label>
                    <div class="space-y-1">
                        <a href="#" class="flex items-center gap-4 px-4 py-4 rounded-2xl text-slate-400 hover:bg-slate-50">
                            <i data-lucide="brain-circuit" class="w-4 h-4" stroke-width="1.5"></i>
                            <span class="text-sm font-medium tracking-tight">Forecasting</span>
                        </a>
                    </div>
                </div>
            </nav>

            <div class="p-10 border-t thin-border">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center font-bold text-xs">
                        AD
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-black tracking-tight truncate">Administrator</p>
                        <p class="text-[10px] lowercase tracking-widest text-slate-400 truncate">system active</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content V5 -->
        <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <header class="h-28 flex items-center justify-between px-16 border-b thin-border bg-white">
                <div>
                    <h2 class="text-3xl font-black tracking-tight italic">Combined Audit</h2>
                    <p class="text-[11px] lowercase tracking-[0.3em] font-light text-slate-400 mt-2">v2 (typography) + v3 (components)</p>
                </div>

                <div class="flex items-center gap-6">
                    <button class="flex items-center gap-3 px-8 py-4 bg-slate-950 text-white rounded-full shadow-2xl shadow-slate-900/20 active:scale-95 transition-all">
                        <i data-lucide="plus" class="w-4 h-4" stroke-width="1.5"></i>
                        <span class="text-xs font-black uppercase tracking-widest">New Entry</span>
                    </button>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto p-16 custom-scrollbar">
                <div class="max-w-6xl mx-auto space-y-20">
                    
                    <!-- Stats Grid with Aggressive Whitespace & Lowercase Labels -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-16">
                        @foreach([
                            ['label' => 'total items', 'val' => '1,248'],
                            ['label' => 'stock volume', 'val' => '8,540'],
                            ['label' => 'net asset value', 'val' => 'Rp 245M'],
                            ['label' => 'system alerts', 'val' => '12']
                        ] as $stat)
                        <div class="space-y-4">
                            <label class="text-[10px] lowercase tracking-[0.5em] font-light text-slate-400 block px-1">{{ $stat['label'] }}</label>
                            <h3 class="text-5xl font-black tracking-tighter italic">{{ $stat['val'] }}</h3>
                            <div class="w-full h-[0.5px] bg-slate-200"></div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Main Dashboard Section -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-16">
                        <div class="lg:col-span-2 space-y-10">
                            <h3 class="text-2xl font-black tracking-tight italic">Inventory Velocity</h3>
                            <div class="bg-white p-12 rounded-[2.5rem] thin-border h-96 flex items-end justify-between gap-4">
                                @for($i = 0; $i < 15; $i++)
                                <div class="flex-1 bg-slate-100 rounded-full relative group cursor-pointer" style="height: {{ rand(30, 90) }}%">
                                    <div class="absolute inset-0 bg-slate-950 scale-y-0 group-hover:scale-y-100 transition-transform origin-bottom rounded-full"></div>
                                </div>
                                @endfor
                            </div>
                        </div>

                        <div class="space-y-10">
                            <h3 class="text-2xl font-black tracking-tight italic">Recent Pulse</h3>
                            <div class="space-y-3">
                                @foreach($recentTransactions as $tx)
                                <div class="bg-white p-6 rounded-3xl thin-border flex items-center gap-5 hover:bg-slate-50 transition-colors cursor-pointer">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center bg-slate-100">
                                        <i data-lucide="{{ $tx->type === 'in' ? 'arrow-down-left' : 'arrow-up-right' }}" class="w-4 h-4 text-slate-950" stroke-width="1.5"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-black tracking-tight truncate">{{ $tx->item->nama_barang }}</p>
                                        <p class="text-[9px] lowercase tracking-widest text-slate-400 mt-1">processed just now</p>
                                    </div>
                                    <p class="text-sm font-black italic">{{ $tx->type === 'in' ? '+' : '-' }}{{ $tx->quantity }}</p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Footnote -->
                    <div class="pt-20 border-t thin-border">
                        <div class="bg-white p-16 rounded-[3rem] thin-border flex flex-col items-center text-center">
                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-8">
                                <i data-lucide="layers" class="w-8 h-8 text-slate-950" stroke-width="1.5"></i>
                            </div>
                            <h4 class="text-2xl font-black tracking-tight italic mb-4">Precision & Breathability</h4>
                            <p class="max-w-xl text-slate-500 font-light leading-loose text-sm">
                                V5 menggabungkan presisi komponen dari V3 (border 0.5px, ikon stroke 1.5) dengan kejelasan tipografi dan ruang dari V2 (font weight 900, lowercase labels, aggressive whitespace). Hasilnya adalah UI yang terasa modern, "high-end", dan sangat mudah dibaca.
                            </p>
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
