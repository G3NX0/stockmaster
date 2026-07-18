<!DOCTYPE html>
<html lang="en" class="h-full" x-data="{ darkMode: true, page: 'overview', sidebarOpen: true }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StockMaster V12: The Living Masterpiece</title>
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
                        onyx: { 950: '#030303', 900: '#0a0a0a', 800: '#141414', 700: '#1c1c1c' }
                    },
                    borderRadius: { 'squircle': '2.5rem' }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; background-color: #ffffff; }
        .dark body { background-color: #000000; }
        
        .living-glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(40px) saturate(180%);
            -webkit-backdrop-filter: blur(40px) saturate(180%);
            border: 0.5px solid rgba(0, 0, 0, 0.1);
            box-shadow: 0 10px 30px -10px rgba(0,0,0,0.05);
            transition: all 0.4s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        .dark .living-glass {
            background: rgba(10, 10, 10, 0.6);
            border: 0.5px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 20px 40px -10px rgba(0,0,0,0.4);
        }

        html:not(.dark) .living-glass {
            background: rgba(240, 240, 240, 0.4);
        }

        .bento-grid {
            display: grid;
            gap: 1.5rem;
            grid-template-columns: repeat(12, 1fr);
        }

        .squircle { border-radius: 2.5rem; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .page-transition { animation: fadeIn 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }

        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(128, 128, 128, 0.2); border-radius: 10px; }
    </style>
</head>
<body class="h-full text-onyx-950 dark:text-white transition-colors duration-500 overflow-hidden">
    
    <div class="flex h-full p-4 lg:p-10 gap-10">
        <!-- Living Sidebar -->
        <aside class="w-72 hidden xl:flex flex-col rounded-squircle living-glass p-8 space-y-12">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-2xl bg-onyx-950 dark:bg-white flex items-center justify-center shadow-xl">
                    <i data-lucide="activity" class="w-5 h-5 text-white dark:text-black"></i>
                </div>
                <h1 class="text-xl font-black tracking-tighter uppercase italic leading-none">StockLive</h1>
            </div>

            <nav class="flex-1 space-y-12">
                <div class="space-y-6">
                    <label class="text-[9px] lowercase tracking-[0.5em] font-light text-onyx-400 block px-4">Navigation</label>
                    <div class="space-y-2">
                        <button @click="page = 'overview'" :class="page === 'overview' ? 'bg-onyx-950 dark:bg-white text-white dark:text-black shadow-xl' : 'text-onyx-500 hover:bg-white/5'" class="w-full flex items-center gap-4 px-5 py-4 rounded-2xl transition-all group">
                            <i data-lucide="layout-grid" class="w-4 h-4" :stroke-width="page === 'overview' ? 2 : 1.5"></i>
                            <span class="text-xs font-bold tracking-tight">Overview</span>
                        </button>
                        <button @click="page = 'inventory'" :class="page === 'inventory' ? 'bg-onyx-950 dark:bg-white text-white dark:text-black shadow-xl' : 'text-onyx-500 hover:bg-white/5'" class="w-full flex items-center gap-4 px-5 py-4 rounded-2xl transition-all group">
                            <i data-lucide="package" class="w-4 h-4" :stroke-width="page === 'inventory' ? 2 : 1.5"></i>
                            <span class="text-xs font-bold tracking-tight">Inventory</span>
                        </button>
                    </div>
                </div>
            </nav>

            <div class="living-glass p-4 rounded-3xl flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-onyx-800 flex items-center justify-center font-bold text-xs text-white">AD</div>
                <div class="flex-1 min-w-0">
                    <p class="text-[10px] font-black truncate">Live Demo</p>
                    <p class="text-[8px] lowercase tracking-widest text-onyx-500 uppercase">V12 MASTER</p>
                </div>
            </div>
        </aside>

        <!-- Main Living Content -->
        <main class="flex-1 flex flex-col gap-8 lg:gap-10 min-w-0 overflow-y-auto custom-scrollbar pb-32 xl:pb-0">
            
            <header class="flex items-center justify-between gap-6">
                <div>
                    <h2 class="text-3xl lg:text-5xl font-black tracking-tighter italic leading-none" x-text="page === 'overview' ? 'Node Overview' : 'Inventory Ledger'"></h2>
                    <p class="text-[10px] lg:text-[12px] lowercase tracking-[0.4em] font-light text-onyx-400 mt-4 uppercase">Real-time data visualization v12</p>
                </div>

                <div class="flex items-center gap-4">
                    <button @click="darkMode = !darkMode" class="w-12 h-12 lg:w-16 lg:h-16 rounded-2xl lg:rounded-3xl living-glass flex items-center justify-center hover:scale-105 transition-all">
                        <i data-lucide="sun" x-show="darkMode" class="w-5 h-5"></i>
                        <i data-lucide="moon" x-show="!darkMode" class="w-5 h-5 text-onyx-950"></i>
                    </button>
                    <button class="px-8 lg:px-12 h-12 lg:h-16 rounded-2xl lg:rounded-3xl bg-onyx-950 dark:bg-white text-white dark:text-black font-black uppercase tracking-widest text-[10px] shadow-2xl">
                        Add Entry
                    </button>
                </div>
            </header>

            <!-- Overview Page -->
            <div x-show="page === 'overview'" class="page-transition space-y-10">
                <div class="bento-grid">
                    <!-- Main Chart Card -->
                    <div class="col-span-12 xl:col-span-8 living-glass rounded-squircle p-8 lg:p-12 min-h-[400px] flex flex-col">
                        <div class="flex items-center justify-between mb-10">
                            <h3 class="text-xl font-black italic">Sync Velocity</h3>
                            <div class="flex gap-4">
                                <span class="text-[9px] font-black uppercase tracking-widest opacity-30">Weekly</span>
                                <span class="text-[9px] font-black uppercase tracking-widest text-emerald-500">Live</span>
                            </div>
                        </div>
                        <div class="flex-1 relative">
                            <canvas id="velocityChart"></canvas>
                        </div>
                    </div>

                    <!-- Side Stats -->
                    <div class="col-span-12 xl:col-span-4 grid grid-cols-1 gap-6">
                        <div class="living-glass rounded-squircle p-8 flex flex-col justify-center text-center">
                            <label class="text-[9px] lowercase tracking-[0.4em] font-light text-onyx-400 mb-2">Total Value</label>
                            <h4 class="text-4xl font-black tracking-tighter italic">Rp 2.45B</h4>
                        </div>
                        <div class="living-glass rounded-squircle p-8 flex flex-col justify-center text-center border-emerald-500/20">
                            <label class="text-[9px] lowercase tracking-[0.4em] font-light text-onyx-400 mb-2">System Health</label>
                            <h4 class="text-4xl font-black tracking-tighter italic text-emerald-500">100%</h4>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                    <div class="living-glass rounded-[2rem] p-10">
                        <h4 class="text-lg font-black italic mb-6">Recent Activities</h4>
                        <div class="space-y-4">
                            @foreach($recentTransactions as $tx)
                            <div class="flex items-center justify-between border-b border-onyx-100 dark:border-white/5 pb-4 last:border-0">
                                <div class="flex items-center gap-4">
                                    <div class="w-8 h-8 rounded-xl bg-onyx-950 dark:bg-white text-white dark:text-black flex items-center justify-center">
                                        <i data-lucide="{{ $tx->type === 'in' ? 'arrow-down' : 'arrow-up' }}" class="w-3 h-3"></i>
                                    </div>
                                    <span class="text-xs font-bold">{{ $tx->item->nama_barang }}</span>
                                </div>
                                <span class="text-xs font-black italic {{ $tx->type === 'in' ? 'text-emerald-500' : 'text-rose-500' }}">{{ $tx->type === 'in' ? '+' : '-' }}{{ $tx->quantity }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="living-glass rounded-[2rem] p-10 flex flex-col">
                        <h4 class="text-lg font-black italic mb-6">Distribution</h4>
                        <div class="flex-1 relative min-h-[200px]">
                            <canvas id="distributionChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inventory Page -->
            <div x-show="page === 'inventory'" class="page-transition space-y-10" x-cloak>
                <div class="living-glass rounded-squircle overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-onyx-950/5 dark:bg-white/5">
                            <tr>
                                <th class="p-8 text-[10px] font-black uppercase tracking-widest text-onyx-400">Item Name</th>
                                <th class="p-8 text-[10px] font-black uppercase tracking-widest text-onyx-400 text-center">Stock</th>
                                <th class="p-8 text-[10px] font-black uppercase tracking-widest text-onyx-400 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-onyx-100 dark:divide-white/5">
                            @foreach($recentTransactions as $tx)
                            <tr class="hover:bg-white/5 transition-colors group">
                                <td class="p-8">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold">{{ $tx->item->nama_barang }}</span>
                                        <span class="text-[9px] text-onyx-500 uppercase tracking-widest">SKU-{{ rand(1000, 9999) }}</span>
                                    </div>
                                </td>
                                <td class="p-8 text-center text-sm font-black italic">{{ rand(100, 1000) }}</td>
                                <td class="p-8 text-right">
                                    <span class="px-4 py-1.5 rounded-full bg-emerald-500/10 text-emerald-500 text-[10px] font-black italic">available</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

    <!-- Mobile Bottom Nav -->
    <nav class="xl:hidden fixed bottom-8 left-1/2 -translate-x-1/2 w-[calc(100%-4rem)] max-w-sm living-glass p-4 rounded-[2rem] flex items-center justify-around z-[100]">
        <button @click="page = 'overview'" :class="page === 'overview' ? 'bg-onyx-950 dark:bg-white text-white dark:text-black' : 'text-onyx-500'" class="p-4 rounded-2xl transition-all">
            <i data-lucide="layout-grid" class="w-6 h-6"></i>
        </button>
        <button @click="page = 'inventory'" :class="page === 'inventory' ? 'bg-onyx-950 dark:bg-white text-white dark:text-black' : 'text-onyx-500'" class="p-4 rounded-2xl transition-all">
            <i data-lucide="package" class="w-6 h-6"></i>
        </button>
    </nav>

    <script>
        lucide.createIcons();

        // Chart Initialization
        document.addEventListener('DOMContentLoaded', () => {
            const ctxVelocity = document.getElementById('velocityChart').getContext('2d');
            const velocityChart = new Chart(ctxVelocity, {
                type: 'line',
                data: {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    datasets: [{
                        label: 'Velocity',
                        data: [65, 59, 80, 81, 56, 55, 40],
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointRadius: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { display: false },
                        x: { grid: { display: false }, ticks: { color: '#71717a', font: { size: 10, weight: '900' } } }
                    }
                }
            });

            const ctxDist = document.getElementById('distributionChart').getContext('2d');
            new Chart(ctxDist, {
                type: 'doughnut',
                data: {
                    labels: ['Tech', 'Life', 'Food'],
                    datasets: [{
                        data: [300, 50, 100],
                        backgroundColor: ['#10b981', '#f59e0b', '#3b82f6'],
                        borderWidth: 0,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    cutout: '70%'
                }
            });

            // Re-render charts on dark mode toggle (simplified)
            window.addEventListener('resize', () => {
                velocityChart.resize();
            });
        });
    </script>
</body>
</html>
