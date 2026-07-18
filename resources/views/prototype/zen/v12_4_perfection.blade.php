<!DOCTYPE html>
<html lang="en" class="h-full" x-data="{ darkMode: true, page: 'dashboard', sheetOpen: false, sidebarGroups: { core: true, data: true, intel: true, ent: true, sys: true } }" 
    :class="{ 'dark': darkMode }" 
    x-effect="initChart(darkMode)">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StockMaster V12.4: The Zenith Perfection</title>
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
        body { font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; background-color: #fcfcfc; }
        .dark body { background-color: #000000; }
        
        .zen-glass {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(50px) saturate(210%);
            -webkit-backdrop-filter: blur(50px) saturate(210%);
            border: 0.5px solid rgba(0, 0, 0, 0.12);
            box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08);
            transition: all 0.5s cubic-bezier(0.17, 0.67, 0.83, 0.67);
        }

        .dark .zen-glass {
            background: rgba(10, 10, 10, 0.75);
            border: 0.5px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 30px 60px -12px rgba(0,0,0,0.5);
        }

        .squircle { border-radius: 2.5rem; }

        @keyframes entrance {
            from { opacity: 0; transform: scale(0.97) translateY(20px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        .stagger { animation: entrance 0.7s cubic-bezier(0.19, 1, 0.22, 1) forwards; opacity: 0; }
        
        /* Bottom Sheet Perfection */
        .bottom-sheet {
            position: fixed; bottom: 0; left: 0; right: 0;
            height: 80vh; z-index: 1000;
            transform: translateY(105%);
            transition: transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
        }
        .bottom-sheet.open { transform: translateY(0); }

        .sheet-handle {
            width: 36px; height: 4px; background: rgba(0, 0, 0, 0.1);
            border-radius: 10px; margin: 12px auto;
        }
        .dark .sheet-handle { background: rgba(255, 255, 255, 0.2); }

        .custom-scrollbar::-webkit-scrollbar { width: 3px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(128, 128, 128, 0.2); border-radius: 10px; }

        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full text-onyx-950 dark:text-white transition-colors duration-500 overflow-hidden">
    
    <div class="flex h-full p-4 lg:p-10 gap-10">
        <!-- Sidebar (Desktop Only) -->
        <aside class="w-80 hidden xl:flex flex-col rounded-[2.5rem] zen-glass p-10 space-y-12 overflow-y-auto custom-scrollbar">
            <div class="flex items-center gap-5 px-2">
                <div class="w-12 h-12 rounded-2xl bg-onyx-950 dark:bg-white flex items-center justify-center shadow-2xl">
                    <i data-lucide="shield-check" class="w-6 h-6 text-white dark:text-black"></i>
                </div>
                <h1 class="text-2xl font-black tracking-tighter uppercase italic leading-none">Zenith</h1>
            </div>

            <nav class="space-y-10">
                @foreach(['Protocol' => ['dashboard', 'scanner', 'items'], 'Workflow' => ['transactions', 'transfers', 'orders'], 'Intelligence' => ['reports', 'logs', 'settings']] as $group => $items)
                <div class="space-y-4">
                    <label class="text-[10px] font-black uppercase tracking-[0.5em] text-onyx-400 px-4">{{ $group }}</label>
                    @foreach($items as $item)
                    <button @click="page = '{{ $item }}'" :class="page === '{{ $item }}' ? 'bg-onyx-950 dark:bg-white text-white dark:text-black shadow-2xl scale-105' : 'text-onyx-500 hover:bg-white/5 hover:translate-x-2'" class="w-full flex items-center gap-5 px-6 py-4 rounded-[1.25rem] transition-all duration-300">
                        <i data-lucide="{{ $item === 'dashboard' ? 'layout-grid' : ($item === 'scanner' ? 'scan-barcode' : ($item === 'items' ? 'package' : 'layers')) }}" class="w-5 h-5"></i>
                        <span class="text-xs font-bold tracking-tight capitalize">{{ $item }}</span>
                    </button>
                    @endforeach
                </div>
                @endforeach
            </nav>
        </aside>

        <!-- Main Workspace -->
        <main class="flex-1 flex flex-col gap-10 min-w-0 overflow-y-auto custom-scrollbar pb-32 xl:pb-0">
            
            <header class="flex items-center justify-between gap-6 px-2 stagger" style="animation-delay: 0.1s">
                <div class="flex items-center gap-6">
                    <!-- Removed Burger Menu for Mobile as requested -->
                    <div>
                        <h2 class="text-4xl lg:text-6xl font-black tracking-tighter italic leading-none uppercase" x-text="page"></h2>
                        <p class="text-[11px] lg:text-[12px] lowercase tracking-[0.6em] font-light text-onyx-400 mt-6 uppercase">Zenith Perfection v12.4</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <button @click="darkMode = !darkMode" class="w-14 h-14 rounded-2xl zen-glass flex items-center justify-center hover:scale-110 active:scale-90 transition-all group">
                        <i data-lucide="sun" x-show="darkMode" class="w-6 h-6"></i>
                        <i data-lucide="moon" x-show="!darkMode" class="w-6 h-6 text-onyx-950"></i>
                    </button>
                </div>
            </header>

            <!-- Content Nodes -->
            <div x-show="page === 'dashboard'" class="space-y-12">
                <div class="grid grid-cols-12 gap-10">
                    <div class="col-span-12 zen-glass rounded-[3rem] p-10 lg:p-16 min-h-[500px] flex flex-col stagger" style="animation-delay: 0.2s">
                        <div class="flex items-center justify-between mb-14">
                            <h3 class="text-2xl font-black italic tracking-tight">Live Intelligence Pulse</h3>
                            <div class="px-5 py-2 rounded-full bg-emerald-500/10 flex items-center gap-3">
                                <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></div>
                                <span class="text-[10px] font-black uppercase tracking-widest text-emerald-500">System Nominal</span>
                            </div>
                        </div>
                        <div class="flex-1 relative">
                            <canvas id="perfectionChart"></canvas>
                        </div>
                    </div>

                    @foreach(['Assets' => '2.4B', 'Stability' => '100%', 'Alerts' => 'None'] as $l => $v)
                    <div class="col-span-12 md:col-span-4 zen-glass rounded-[2.5rem] p-12 text-center stagger" style="animation-delay: {{ 0.3 + ($loop->index * 0.1) }}s">
                        <span class="text-[9px] font-black tracking-[0.6em] text-onyx-400 uppercase mb-5 block">{{ $l }}</span>
                        <h4 class="text-4xl lg:text-5xl font-black italic tracking-tighter">{{ $v }}</h4>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Page Load Placeholder -->
            <div x-show="page !== 'dashboard'" class="flex items-center justify-center min-h-[500px]" x-cloak>
                <div class="text-center space-y-8 stagger">
                    <div class="w-20 h-20 rounded-3xl bg-onyx-950/5 dark:bg-white/5 flex items-center justify-center animate-pulse">
                        <i data-lucide="zap" class="w-10 h-10 opacity-20"></i>
                    </div>
                    <p class="text-[11px] font-black uppercase tracking-[1.2em] opacity-30">Accessing <span x-text="page"></span></p>
                </div>
            </div>

        </main>
    </div>

    <!-- The Zenith Hub (Mobile Navigation Overlay) -->
    <div x-show="sheetOpen" class="fixed inset-0 z-[900] bg-black/70 backdrop-blur-md" @click="sheetOpen = false" x-cloak x-transition:opacity></div>
    
    <div class="bottom-sheet xl:hidden zen-glass rounded-t-[3.5rem] shadow-[0_-20px_80px_rgba(0,0,0,0.6)]" :class="{ 'open': sheetOpen }">
        <div class="sheet-handle" @click="sheetOpen = false"></div>
        
        <div class="p-12 space-y-14 h-full overflow-y-auto custom-scrollbar">
            <h3 class="text-4xl font-black italic tracking-tighter">System Hub</h3>
            
            <div class="grid grid-cols-3 gap-8">
                @foreach([
                    'dashboard' => 'layout-grid', 'scanner' => 'scan-barcode', 'items' => 'package', 
                    'reports' => 'bar-chart-3', 'transfers' => 'send', 'settings' => 'settings'
                ] as $key => $icon)
                <button @click="page = '{{ $key }}'; sheetOpen = false" class="flex flex-col items-center gap-4 group">
                    <div class="w-16 h-16 rounded-[1.5rem] bg-onyx-950 dark:bg-white text-white dark:text-black flex items-center justify-center shadow-2xl active:scale-90 transition-transform">
                        <i data-lucide="{{ $icon }}" class="w-7 h-7"></i>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-onyx-400">{{ $key }}</span>
                </button>
                @endforeach
            </div>

            <div class="pt-10 border-t border-black/5 dark:border-white/5">
                <label class="text-[10px] font-black uppercase tracking-[0.6em] text-onyx-400 mb-8 block">All Protocols</label>
                <div class="grid grid-cols-2 gap-4">
                    @foreach(['categories', 'units', 'transactions', 'customers', 'orders', 'assets', 'logs', 'users', 'backups'] as $node)
                    <button @click="page = '{{ $node }}'; sheetOpen = false" class="text-left px-8 py-5 rounded-2xl bg-black/5 dark:bg-white/5 text-[11px] font-black uppercase tracking-widest hover:bg-onyx-950 hover:text-white dark:hover:bg-white dark:hover:text-black transition-all">
                        {{ $node }}
                    </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Central Trigger -->
    <div class="xl:hidden fixed bottom-10 left-1/2 -translate-x-1/2 z-[100]">
        <button @click="sheetOpen = true" class="w-24 h-24 rounded-full bg-onyx-950 dark:bg-white text-white dark:text-black shadow-[0_25px_60px_rgba(0,0,0,0.5)] flex items-center justify-center hover:scale-110 active:scale-95 transition-all border-8 border-white/5">
            <i data-lucide="layout-grid" class="w-10 h-10"></i>
        </button>
    </div>

    <script>
        lucide.createIcons();

        let chart;
        function initChart(dark) {
            const canvas = document.getElementById('perfectionChart');
            if(!canvas) return;
            const ctx = canvas.getContext('2d');
            if(chart) chart.destroy();

            // ABSOLUTE HIGH CONTRAST COLORS
            const accentColor = dark ? '#ffffff' : '#000000'; 
            const gridColor = dark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.1)';
            const fillColor = dark ? 'rgba(255,255,255,0.03)' : 'rgba(0,0,0,0.05)';
            const labelColor = dark ? '#71717a' : '#000000';

            chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug'],
                    datasets: [{
                        data: [25, 45, 35, 75, 55, 90, 65, 100],
                        borderColor: accentColor,
                        borderWidth: 6,
                        tension: 0.4,
                        pointRadius: 0,
                        fill: true,
                        backgroundColor: fillColor
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    animation: { duration: 1000, easing: 'easeOutQuart' },
                    plugins: { legend: { display: false } },
                    scales: { 
                        y: { 
                            grid: { color: gridColor, drawBorder: false },
                            ticks: { 
                                color: labelColor, 
                                font: { size: 10, weight: '900' },
                                padding: 10
                            }
                        }, 
                        x: { 
                            grid: { display: false }, 
                            ticks: { 
                                color: labelColor, 
                                font: { size: 10, weight: '900' },
                                padding: 10
                            } 
                        } 
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Initial render based on initial x-data state
            setTimeout(() => {
                const isDark = document.documentElement.classList.contains('dark');
                initChart(isDark);
            }, 100);
        });

        // Use a cleaner way to watch for theme changes via Alpine
        document.addEventListener('alpine:init', () => {
            // No need for complicated logic, we can just use x-effect in the HTML tag
        });
    </script>
</body>
</html>
