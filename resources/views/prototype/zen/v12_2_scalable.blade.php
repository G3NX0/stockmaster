<!DOCTYPE html>
<html lang="en" class="h-full" x-data="{ darkMode: true, page: 'dashboard', appsOpen: false, sidebarGroups: { core: true, data: true, intel: true, ent: true, sys: true } }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StockMaster V12.2: Scalable Masterpiece</title>
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
                        onyx: { 950: '#020202', 900: '#080808', 800: '#101010', 700: '#181818' }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; background-color: #ffffff; }
        .dark body { background-color: #000000; }
        
        .scalable-glass {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(50px) saturate(200%);
            -webkit-backdrop-filter: blur(50px) saturate(200%);
            border: 0.5px solid rgba(0, 0, 0, 0.1);
            box-shadow: 0 15px 35px -10px rgba(0,0,0,0.05);
        }

        .dark .scalable-glass {
            background: rgba(8, 8, 8, 0.65);
            border: 0.5px solid rgba(255, 255, 255, 0.06);
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.4);
        }

        .squircle { border-radius: 2.5rem; }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .page-node { animation: slideUp 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }

        .custom-scrollbar::-webkit-scrollbar { width: 3px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(128, 128, 128, 0.2); border-radius: 10px; }

        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full text-onyx-950 dark:text-white transition-colors duration-500 overflow-hidden">
    
    <div class="flex h-full p-4 lg:p-8 gap-8">
        <!-- V12.2 Grouped Sidebar (Desktop Only) -->
        <aside class="w-80 hidden xl:flex flex-col rounded-[2.5rem] scalable-glass p-8 space-y-8 overflow-y-auto custom-scrollbar">
            <div class="flex items-center gap-4 px-2 mb-4">
                <div class="w-10 h-10 rounded-2xl bg-onyx-950 dark:bg-white flex items-center justify-center shadow-2xl">
                    <i data-lucide="layers" class="w-5 h-5 text-white dark:text-black"></i>
                </div>
                <h1 class="text-xl font-black tracking-tighter uppercase italic leading-none">Enterprise</h1>
            </div>

            <nav class="space-y-6">
                <!-- Group: Core -->
                <div class="space-y-2">
                    <button @click="sidebarGroups.core = !sidebarGroups.core" class="w-full flex items-center justify-between px-4 text-[9px] font-black uppercase tracking-[0.4em] text-onyx-400">
                        <span>Core Protocol</span>
                        <i data-lucide="chevron-down" class="w-3 h-3 transition-transform" :class="!sidebarGroups.core ? '-rotate-90' : ''"></i>
                    </button>
                    <div x-show="sidebarGroups.core" x-collapse class="space-y-1">
                        @foreach(['dashboard' => 'layout-grid', 'scanner' => 'scan-barcode', 'items' => 'package', 'transactions' => 'arrow-left-right'] as $key => $icon)
                        <button @click="page = '{{ $key }}'" :class="page === '{{ $key }}' ? 'bg-onyx-950 dark:bg-white text-white dark:text-black shadow-lg' : 'text-onyx-500 hover:bg-white/5'" class="w-full flex items-center gap-4 px-5 py-3.5 rounded-2xl transition-all">
                            <i data-lucide="{{ $icon }}" class="w-4 h-4" :stroke-width="page === '{{ $key }}' ? 2.5 : 1.5"></i>
                            <span class="text-xs font-bold tracking-tight capitalize">{{ $key }}</span>
                        </button>
                        @endforeach
                    </div>
                </div>

                <!-- Group: Intelligence -->
                <div class="space-y-2">
                    <button @click="sidebarGroups.intel = !sidebarGroups.intel" class="w-full flex items-center justify-between px-4 text-[9px] font-black uppercase tracking-[0.4em] text-onyx-400">
                        <span>Intelligence</span>
                        <i data-lucide="chevron-down" class="w-3 h-3 transition-transform" :class="!sidebarGroups.intel ? '-rotate-90' : ''"></i>
                    </button>
                    <div x-show="sidebarGroups.intel" x-collapse class="space-y-1">
                        @foreach(['reports' => 'bar-chart-3', 'forecasting' => 'trending-up', 'heatmap' => 'map', 'profit' => 'pie-chart'] as $key => $icon)
                        <button @click="page = '{{ $key }}'" :class="page === '{{ $key }}' ? 'bg-onyx-950 dark:bg-white text-white dark:text-black shadow-lg' : 'text-onyx-500 hover:bg-white/5'" class="w-full flex items-center gap-4 px-5 py-3.5 rounded-2xl transition-all">
                            <i data-lucide="{{ $icon }}" class="w-4 h-4" :stroke-width="page === '{{ $key }}' ? 2.5 : 1.5"></i>
                            <span class="text-xs font-bold tracking-tight capitalize">{{ $key }}</span>
                        </button>
                        @endforeach
                    </div>
                </div>

                <!-- Group: Enterprise -->
                <div class="space-y-2">
                    <button @click="sidebarGroups.ent = !sidebarGroups.ent" class="w-full flex items-center justify-between px-4 text-[9px] font-black uppercase tracking-[0.4em] text-onyx-400">
                        <span>Ecosystem</span>
                        <i data-lucide="chevron-down" class="w-3 h-3 transition-transform" :class="!sidebarGroups.ent ? '-rotate-90' : ''"></i>
                    </button>
                    <div x-show="sidebarGroups.ent" x-collapse class="space-y-1">
                        @foreach(['customers' => 'users', 'transfers' => 'send', 'orders' => 'shopping-bag', 'assets' => 'gem'] as $key => $icon)
                        <button @click="page = '{{ $key }}'" :class="page === '{{ $key }}' ? 'bg-onyx-950 dark:bg-white text-white dark:text-black shadow-lg' : 'text-onyx-500 hover:bg-white/5'" class="w-full flex items-center gap-4 px-5 py-3.5 rounded-2xl transition-all">
                            <i data-lucide="{{ $icon }}" class="w-4 h-4" :stroke-width="page === '{{ $key }}' ? 2.5 : 1.5"></i>
                            <span class="text-xs font-bold tracking-tight capitalize">{{ $key }}</span>
                        </button>
                        @endforeach
                    </div>
                </div>
            </nav>

            <div class="mt-auto p-4 rounded-3xl scalable-glass border thin-border flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-onyx-800 flex items-center justify-center font-bold text-xs text-white">AD</div>
                <div class="flex-1 min-w-0">
                    <p class="text-[10px] font-black truncate">Admin Prime</p>
                    <p class="text-[8px] lowercase tracking-widest text-onyx-500 uppercase">Enterprise v12.2</p>
                </div>
            </div>
        </aside>

        <!-- Main Living Content -->
        <main class="flex-1 flex flex-col gap-8 min-w-0 overflow-y-auto custom-scrollbar pb-32 xl:pb-0">
            
            <header class="flex items-center justify-between gap-6 px-2">
                <div class="flex items-center gap-6">
                    <button @click="appsOpen = true" class="xl:hidden w-12 h-12 rounded-2xl scalable-glass flex items-center justify-center shadow-xl">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                    <div>
                        <h2 class="text-3xl lg:text-5xl font-black tracking-tighter italic leading-none uppercase" x-text="page"></h2>
                        <p class="text-[10px] lg:text-[11px] lowercase tracking-[0.5em] font-light text-onyx-400 mt-4 uppercase">Scalability Test Protocol</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <button @click="darkMode = !darkMode" class="w-12 h-12 lg:w-14 lg:h-14 rounded-2xl scalable-glass flex items-center justify-center hover:scale-105 transition-all">
                        <i data-lucide="sun" x-show="darkMode" class="w-5 h-5"></i>
                        <i data-lucide="moon" x-show="!darkMode" class="w-5 h-5 text-onyx-950"></i>
                    </button>
                </div>
            </header>

            <!-- Dashboard Content -->
            <div x-show="page === 'dashboard'" class="page-node space-y-10">
                <div class="grid grid-cols-12 gap-8">
                    <div class="col-span-12 lg:col-span-9 scalable-glass rounded-[2.5rem] p-10 lg:p-14 min-h-[400px] flex flex-col">
                        <h3 class="text-lg font-black italic mb-10">Global Velocity</h3>
                        <div class="flex-1 relative"><canvas id="mainChart"></canvas></div>
                    </div>
                    <div class="col-span-12 lg:col-span-3 grid grid-cols-1 gap-8">
                        @foreach(['Stock' => '8.5K', 'Value' => '245M', 'Low' => '12'] as $l => $v)
                        <div class="scalable-glass rounded-[2rem] p-8 text-center flex flex-col justify-center">
                            <span class="text-[8px] font-black tracking-widest text-onyx-400 uppercase mb-2">{{$l}}</span>
                            <h4 class="text-3xl font-black italic leading-none">{{$v}}</h4>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Placeholder for other pages -->
            <div x-show="page !== 'dashboard'" class="page-node flex items-center justify-center min-h-[500px]" x-cloak>
                <div class="text-center space-y-4">
                    <i data-lucide="loader-2" class="w-12 h-12 animate-spin mx-auto opacity-20"></i>
                    <p class="text-xs font-black uppercase tracking-[0.8em] opacity-40">Connecting to <span x-text="page"></span> node...</p>
                </div>
            </div>

        </main>
    </div>

    <!-- Mobile Hybrid Bottom Nav -->
    <nav class="xl:hidden fixed bottom-6 left-1/2 -translate-x-1/2 w-[calc(100%-3rem)] max-w-sm scalable-glass p-3 rounded-[2rem] flex items-center justify-around z-[100]">
        @foreach(['dashboard' => 'layout-grid', 'items' => 'package', 'transactions' => 'arrow-left-right', 'reports' => 'bar-chart-3'] as $key => $icon)
        <button @click="page = '{{ $key }}'" 
            :class="page === '{{ $key }}' ? 'bg-onyx-950 dark:bg-white text-white dark:text-black shadow-xl' : 'text-onyx-500'" 
            class="p-4 rounded-[1.5rem] transition-all">
            <i data-lucide="{{ $icon }}" class="w-5 h-5"></i>
        </button>
        @endforeach
        <button @click="appsOpen = true" class="p-4 rounded-[1.5rem] text-onyx-500 bg-onyx-950/5 dark:bg-white/5">
            <i data-lucide="plus" class="w-6 h-6"></i>
        </button>
    </nav>

    <!-- Apps Overlay (Full Glass) -->
    <div x-show="appsOpen" 
        x-transition:enter="transition ease-out duration-500"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed inset-0 z-[200] p-8 flex items-center justify-center bg-black/40 backdrop-blur-3xl" x-cloak>
        
        <div class="w-full max-w-2xl scalable-glass rounded-[3rem] p-12 relative max-h-[80vh] overflow-y-auto custom-scrollbar">
            <button @click="appsOpen = false" class="absolute top-10 right-10 w-12 h-12 rounded-full bg-white/10 flex items-center justify-center hover:bg-white/20">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
            
            <h3 class="text-3xl font-black italic tracking-tighter mb-12">All Protocols</h3>
            
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-6">
                @foreach([
                    'dashboard' => 'layout-grid', 'scanner' => 'scan-barcode', 'items' => 'package', 
                    'categories' => 'tag', 'units' => 'ruler', 'transactions' => 'arrow-left-right',
                    'reports' => 'bar-chart-3', 'forecasting' => 'trending-up', 'heatmap' => 'map',
                    'customers' => 'users', 'transfers' => 'send', 'orders' => 'shopping-bag',
                    'assets' => 'gem', 'logs' => 'history', 'users' => 'user-plus', 'backups' => 'database'
                ] as $key => $icon)
                <button @click="page = '{{ $key }}'; appsOpen = false" class="flex flex-col items-center gap-4 p-6 rounded-3xl hover:bg-white/5 transition-all group">
                    <div class="w-14 h-14 rounded-2xl bg-onyx-950 dark:bg-white/5 flex items-center justify-center shadow-xl group-hover:scale-110 transition-transform">
                        <i data-lucide="{{ $icon }}" class="w-6 h-6 text-white dark:text-white"></i>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-onyx-400 group-hover:text-white">{{ $key }}</span>
                </button>
                @endforeach
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
        document.addEventListener('DOMContentLoaded', () => {
            const ctx = document.getElementById('mainChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        data: [12, 19, 13, 25, 22, 30],
                        borderColor: '#ffffff',
                        borderWidth: 4,
                        tension: 0.4,
                        pointRadius: 0,
                        fill: true,
                        backgroundColor: 'rgba(255, 255, 255, 0.03)'
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { display: false }, x: { grid: { display: false }, ticks: { color: '#71717a', font: { size: 9, weight: '900' } } } }
                }
            });
        });
    </script>
</body>
</html>
