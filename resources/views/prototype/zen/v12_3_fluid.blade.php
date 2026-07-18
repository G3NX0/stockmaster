<!DOCTYPE html>
<html lang="en" class="h-full" x-data="{ darkMode: true, page: 'dashboard', sheetOpen: false, sidebarGroups: { core: true, data: true, intel: true, ent: true, sys: true } }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StockMaster V12.3: The Fluid Zen</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;300;400;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        onyx: { 950: '#010101', 900: '#080808', 800: '#101010', 700: '#181818' }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; background-color: #f8f8f8; }
        .dark body { background-color: #000000; }
        
        .fluid-glass {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(50px) saturate(200%);
            -webkit-backdrop-filter: blur(50px) saturate(200%);
            border: 0.5px solid rgba(0, 0, 0, 0.1);
            box-shadow: 0 10px 30px -10px rgba(0,0,0,0.05);
            transition: all 0.5s cubic-bezier(0.17, 0.67, 0.83, 0.67);
        }

        .dark .fluid-glass {
            background: rgba(10, 10, 10, 0.7);
            border: 0.5px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 20px 40px -10px rgba(0,0,0,0.4);
        }

        .squircle { border-radius: 2.5rem; }

        @keyframes entrance {
            from { opacity: 0; transform: scale(0.96) translateY(20px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        .stagger { animation: entrance 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; opacity: 0; }
        
        /* Bottom Sheet Styles */
        .bottom-sheet {
            position: fixed; bottom: 0; left: 0; right: 0;
            height: 85vh; z-index: 1000;
            transform: translateY(100%);
            transition: transform 0.6s cubic-bezier(0.19, 1, 0.22, 1);
        }
        .bottom-sheet.open { transform: translateY(0); }

        .sheet-handle {
            width: 40px; height: 5px; background: rgba(128, 128, 128, 0.3);
            border-radius: 10px; margin: 15px auto;
        }

        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar { width: 3px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(128, 128, 128, 0.2); border-radius: 10px; }

        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full text-onyx-950 dark:text-white transition-colors duration-500 overflow-hidden">
    
    <div class="flex h-full p-4 lg:p-8 gap-8">
        <!-- Fluid Sidebar (Desktop) -->
        <aside class="w-80 hidden xl:flex flex-col rounded-[2.5rem] fluid-glass p-8 space-y-10 overflow-y-auto custom-scrollbar">
            <div class="flex items-center gap-4 px-2 mb-4">
                <div class="w-10 h-10 rounded-2xl bg-onyx-950 dark:bg-white flex items-center justify-center shadow-2xl">
                    <i data-lucide="zap" class="w-6 h-6 text-white dark:text-black"></i>
                </div>
                <h1 class="text-xl font-black tracking-tighter uppercase italic leading-none">Fluid Zen</h1>
            </div>

            <nav class="space-y-8">
                @foreach(['Core' => ['dashboard', 'scanner', 'items'], 'Assets' => ['transactions', 'transfers', 'orders'], 'System' => ['reports', 'logs', 'settings']] as $group => $items)
                <div class="space-y-3">
                    <label class="text-[9px] font-black uppercase tracking-[0.4em] text-onyx-400 px-4">{{ $group }}</label>
                    @foreach($items as $item)
                    <button @click="page = '{{ $item }}'" :class="page === '{{ $item }}' ? 'bg-onyx-950 dark:bg-white text-white dark:text-black shadow-xl' : 'text-onyx-500 hover:bg-white/5'" class="w-full flex items-center gap-4 px-5 py-3.5 rounded-2xl transition-all">
                        <i data-lucide="{{ $item === 'dashboard' ? 'layout-grid' : ($item === 'scanner' ? 'scan-barcode' : ($item === 'items' ? 'package' : 'layers')) }}" class="w-4 h-4"></i>
                        <span class="text-xs font-bold tracking-tight capitalize">{{ $item }}</span>
                    </button>
                    @endforeach
                </div>
                @endforeach
            </nav>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col gap-8 min-w-0 overflow-y-auto custom-scrollbar pb-32 xl:pb-0">
            
            <header class="flex items-center justify-between gap-6 px-2 stagger" style="animation-delay: 0.1s">
                <div class="flex items-center gap-6">
                    <button @click="sheetOpen = true" class="xl:hidden w-12 h-12 rounded-2xl fluid-glass flex items-center justify-center shadow-xl">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                    <div>
                        <h2 class="text-3xl lg:text-5xl font-black tracking-tighter italic leading-none uppercase" x-text="page"></h2>
                        <p class="text-[10px] lg:text-[11px] lowercase tracking-[0.5em] font-light text-onyx-400 mt-4 uppercase">Fluid interaction v12.3</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <button @click="darkMode = !darkMode" class="w-12 h-12 lg:w-14 lg:h-14 rounded-2xl fluid-glass flex items-center justify-center hover:scale-110 active:scale-90 transition-all">
                        <i data-lucide="sun" x-show="darkMode" class="w-5 h-5"></i>
                        <i data-lucide="moon" x-show="!darkMode" class="w-5 h-5 text-onyx-950"></i>
                    </button>
                </div>
            </header>

            <!-- Adaptive Dashboard -->
            <div x-show="page === 'dashboard'" class="space-y-10">
                <div class="grid grid-cols-12 gap-8">
                    <div class="col-span-12 lg:col-span-12 fluid-glass rounded-[2.5rem] p-8 lg:p-14 min-h-[450px] flex flex-col stagger" style="animation-delay: 0.2s">
                        <div class="flex items-center justify-between mb-10">
                            <h3 class="text-xl font-black italic">Live Intelligence Pulse</h3>
                            <div class="flex gap-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                    <span class="text-[9px] font-black uppercase tracking-widest text-emerald-500">Active Node</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex-1 relative">
                            <canvas id="fluidChart"></canvas>
                        </div>
                    </div>

                    @foreach(['Assets' => '2.4B', 'Sync' => '99%', 'Alerts' => '0'] as $l => $v)
                    <div class="col-span-12 md:col-span-4 fluid-glass rounded-[2rem] p-10 text-center stagger" style="animation-delay: {{ 0.3 + ($loop->index * 0.1) }}s">
                        <span class="text-[8px] font-black tracking-[0.5em] text-onyx-400 uppercase mb-4 block">{{ $l }}</span>
                        <h4 class="text-4xl font-black italic">{{ $v }}</h4>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Loader for other pages -->
            <div x-show="page !== 'dashboard'" class="flex items-center justify-center min-h-[500px]" x-cloak>
                <div class="text-center space-y-6 stagger">
                    <div class="w-16 h-16 rounded-full border-4 border-white/10 border-t-onyx-950 dark:border-t-white animate-spin mx-auto"></div>
                    <p class="text-[10px] font-black uppercase tracking-[1em] opacity-40">Fluid transition to <span x-text="page"></span></p>
                </div>
            </div>

        </main>
    </div>

    <!-- Zen Bottom Sheet (Mobile Navigation) -->
    <div x-show="sheetOpen" class="fixed inset-0 z-[900] bg-black/60 backdrop-blur-sm" @click="sheetOpen = false" x-cloak x-transition:opacity></div>
    
    <div class="bottom-sheet xl:hidden fluid-glass rounded-t-[3rem] shadow-[-20px_0_60px_rgba(0,0,0,0.5)]" :class="{ 'open': sheetOpen }">
        <div class="sheet-handle" @click="sheetOpen = false"></div>
        
        <div class="p-10 space-y-12 h-full overflow-y-auto custom-scrollbar">
            <h3 class="text-3xl font-black italic tracking-tighter">Quick Access</h3>
            
            <div class="grid grid-cols-3 gap-6">
                @foreach([
                    'dashboard' => 'layout-grid', 'scanner' => 'scan-barcode', 'items' => 'package', 
                    'reports' => 'bar-chart-3', 'transfers' => 'send', 'settings' => 'settings'
                ] as $key => $icon)
                <button @click="page = '{{ $key }}'; sheetOpen = false" class="flex flex-col items-center gap-3 p-4 rounded-3xl hover:bg-white/5 transition-all group">
                    <div class="w-14 h-14 rounded-2xl bg-onyx-950 dark:bg-white/5 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                        <i data-lucide="{{ $icon }}" class="w-6 h-6 text-white dark:text-white"></i>
                    </div>
                    <span class="text-[9px] font-black uppercase tracking-widest text-onyx-400">{{ $key }}</span>
                </button>
                @endforeach
            </div>

            <div class="space-y-6 pt-10 border-t border-white/5">
                <label class="text-[9px] font-black uppercase tracking-[0.5em] text-onyx-400">All Nodes</label>
                <div class="grid grid-cols-2 gap-4">
                    @foreach(['categories', 'units', 'transactions', 'customers', 'orders', 'assets', 'logs', 'users', 'backups'] as $node)
                    <button @click="page = '{{ $node }}'; sheetOpen = false" class="text-left px-6 py-4 rounded-2xl bg-white/5 text-[10px] font-bold uppercase tracking-widest hover:bg-onyx-950 hover:text-white transition-all">
                        {{ $node }}
                    </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Trigger Hub -->
    <div class="xl:hidden fixed bottom-8 left-1/2 -translate-x-1/2 z-[100]">
        <button @click="sheetOpen = true" class="w-20 h-20 rounded-full bg-onyx-950 dark:bg-white text-white dark:text-black shadow-[0_20px_50px_rgba(0,0,0,0.4)] flex items-center justify-center hover:scale-110 active:scale-90 transition-all">
            <i data-lucide="layout-grid" class="w-8 h-8"></i>
        </button>
    </div>

    <script>
        lucide.createIcons();

        let chart;
        function initChart(dark) {
            const ctx = document.getElementById('fluidChart').getContext('2d');
            if(chart) chart.destroy();

            const accentColor = dark ? '#ffffff' : '#18181b';
            const gridColor = dark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
            const fillColor = dark ? 'rgba(255,255,255,0.05)' : 'rgba(24,24,27,0.05)';

            chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug'],
                    datasets: [{
                        data: [20, 45, 28, 60, 42, 75, 55, 90],
                        borderColor: accentColor,
                        borderWidth: 5,
                        tension: 0.4,
                        pointRadius: 0,
                        fill: true,
                        backgroundColor: fillColor
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    animation: { duration: 2000, easing: 'easeOutQuart' },
                    plugins: { legend: { display: false } },
                    scales: { 
                        y: { 
                            grid: { color: gridColor },
                            ticks: { color: dark ? '#525252' : '#a1a1aa', font: { size: 10, weight: 'bold' } }
                        }, 
                        x: { 
                            grid: { display: false }, 
                            ticks: { color: dark ? '#525252' : '#a1a1aa', font: { size: 10, weight: '900' } } 
                        } 
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            initChart(true);
            
            // Re-init chart when theme changes via Alpine
            window.addEventListener('resize', () => chart.resize());
        });

        // Watch for Alpine's darkMode
        document.addEventListener('alpine:init', () => {
            Alpine.effect(() => {
                const dark = Alpine.store('darkMode', true); // Just a fallback, logic handled by x-data
                // Using timeout to ensure DOM update
                setTimeout(() => {
                    const isDark = document.documentElement.classList.contains('dark');
                    initChart(isDark);
                }, 10);
            });
        });
    </script>
</body>
</html>
