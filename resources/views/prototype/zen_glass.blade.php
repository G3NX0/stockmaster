<!DOCTYPE html>
<html lang="en" class="h-full" x-data="{ darkMode: true }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StockMaster Zen-Glass Prototype</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        zen: {
                            blue: '#3b82f6',
                            slate: {
                                50: '#f8fafc',
                                900: '#0f172a',
                                950: '#020617'
                            }
                        }
                    },
                    borderRadius: {
                        'zen': '1.25rem',
                    }
                }
            }
        }
    </script>
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            -webkit-font-smoothing: antialiased;
        }
        
        .zen-glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(40px);
            -webkit-backdrop-filter: blur(40px);
            border: 0.5px solid rgba(255, 255, 255, 0.08);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .dark .zen-glass {
            background: rgba(255, 255, 255, 0.02);
            border: 0.5px solid rgba(255, 255, 255, 0.05);
        }

        html:not(.dark) .zen-glass {
            background: rgba(15, 23, 42, 0.02);
            border: 0.5px solid rgba(15, 23, 42, 0.05);
        }

        .zen-card-hover:hover {
            transform: scale(1.01);
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.15);
        }

        .mesh-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background-color: #f8fafc;
            background-image: 
                radial-gradient(at 0% 0%, rgba(59, 130, 246, 0.05) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(59, 130, 246, 0.05) 0px, transparent 50%);
        }

        .dark .mesh-background {
            background-color: #020617;
            background-image: 
                radial-gradient(at 0% 0%, rgba(59, 130, 246, 0.03) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(59, 130, 246, 0.03) 0px, transparent 50%);
        }

        @keyframes zen-fade-in {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-zen {
            animation: zen-fade-in 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
            opacity: 0;
        }

        .stagger-1 { animation-delay: 0.1s; }
        .stagger-2 { animation-delay: 0.2s; }
        .stagger-3 { animation-delay: 0.3s; }
        .stagger-4 { animation-delay: 0.4s; }
    </style>
</head>
<body class="h-full text-slate-900 dark:text-slate-100 transition-colors duration-500 overflow-x-hidden">
    <div class="mesh-background"></div>

    <div class="flex h-full">
        <!-- Sidebar Zen -->
        <aside class="w-72 border-r border-slate-200/50 dark:border-white/5 flex flex-col relative z-50 bg-white/5 dark:bg-black/5 backdrop-blur-3xl">
            <div class="p-10">
                <div class="flex items-center gap-4">
                    <div class="w-8 h-8 rounded-full bg-zen-blue flex items-center justify-center shadow-lg shadow-blue-500/20">
                        <i data-lucide="package" class="w-4 h-4 text-white" stroke-width="1.5"></i>
                    </div>
                    <h1 class="text-xl font-black tracking-tighter uppercase italic">StockMaster</h1>
                </div>
            </div>

            <nav class="flex-1 px-8 space-y-10">
                <div class="space-y-4">
                    <label class="text-[10px] lowercase tracking-[0.3em] font-light text-slate-400 block px-2">Navigation</label>
                    <div class="space-y-1">
                        <a href="#" class="flex items-center gap-4 px-4 py-3 rounded-zen bg-zen-blue/10 text-zen-blue transition-all group">
                            <i data-lucide="layout-grid" class="w-4 h-4" stroke-width="1.5"></i>
                            <span class="text-sm font-bold tracking-tight">Overview</span>
                        </a>
                        <a href="#" class="flex items-center gap-4 px-4 py-3 rounded-zen text-slate-400 hover:bg-slate-500/5 hover:text-slate-200 transition-all group">
                            <i data-lucide="package" class="w-4 h-4" stroke-width="1.5"></i>
                            <span class="text-sm font-medium tracking-tight">Inventory</span>
                        </a>
                        <a href="#" class="flex items-center gap-4 px-4 py-3 rounded-zen text-slate-400 hover:bg-slate-500/5 hover:text-slate-200 transition-all group">
                            <i data-lucide="bar-chart-3" class="w-4 h-4" stroke-width="1.5"></i>
                            <span class="text-sm font-medium tracking-tight">Analytics</span>
                        </a>
                    </div>
                </div>

                <div class="space-y-4">
                    <label class="text-[10px] lowercase tracking-[0.3em] font-light text-slate-400 block px-2">Enterprise</label>
                    <div class="space-y-1">
                        <a href="#" class="flex items-center gap-4 px-4 py-3 rounded-zen text-slate-400 hover:bg-slate-500/5 hover:text-slate-200 transition-all group">
                            <i data-lucide="users" class="w-4 h-4" stroke-width="1.5"></i>
                            <span class="text-sm font-medium tracking-tight">Customers</span>
                        </a>
                        <a href="#" class="flex items-center gap-4 px-4 py-3 rounded-zen text-slate-400 hover:bg-slate-500/5 hover:text-slate-200 transition-all group">
                            <i data-lucide="shopping-cart" class="w-4 h-4" stroke-width="1.5"></i>
                            <span class="text-sm font-medium tracking-tight">Procurement</span>
                        </a>
                    </div>
                </div>
            </nav>

            <div class="p-10 border-t border-slate-200/50 dark:border-white/5">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-slate-800 border border-white/10 flex items-center justify-center font-bold text-xs text-white">
                        AD
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-black tracking-tight truncate">Administrator</p>
                        <p class="text-[10px] lowercase tracking-widest text-slate-500 truncate">active session</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Zen -->
        <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <header class="h-24 flex items-center justify-between px-12 border-b border-slate-200/50 dark:border-white/5">
                <div>
                    <h2 class="text-2xl font-black tracking-tight">Dashboard</h2>
                    <p class="text-[11px] lowercase tracking-[0.2em] font-light text-slate-400 mt-1">Real-time inventory intelligence</p>
                </div>

                <div class="flex items-center gap-6">
                    <button @click="darkMode = !darkMode" class="p-3 rounded-full zen-glass hover:scale-110 transition-transform">
                        <i data-lucide="sun" x-show="darkMode" class="w-4 h-4"></i>
                        <i data-lucide="moon" x-show="!darkMode" class="w-4 h-4"></i>
                    </button>
                    <button class="flex items-center gap-3 px-6 py-3 bg-zen-blue text-white rounded-full shadow-xl shadow-blue-500/20 hover:scale-105 active:scale-95 transition-all">
                        <i data-lucide="plus" class="w-4 h-4" stroke-width="1.5"></i>
                        <span class="text-xs font-black uppercase tracking-widest">New Entry</span>
                    </button>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto p-12 custom-scrollbar">
                <div class="max-w-7xl mx-auto space-y-12">
                    
                    <!-- Aggressive Whitespace Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                        <!-- Stat Item -->
                        <div class="animate-zen stagger-1 group">
                            <label class="text-[10px] lowercase tracking-[0.4em] font-light text-slate-400 mb-2 block">Total Assets</label>
                            <div class="flex items-baseline gap-2">
                                <h3 class="text-4xl font-black tracking-tighter italic">1,248</h3>
                                <span class="text-[10px] font-bold text-emerald-500 tracking-widest">+2.4%</span>
                            </div>
                            <div class="w-full h-[0.5px] bg-slate-200 dark:bg-white/10 mt-6 group-hover:bg-zen-blue transition-colors"></div>
                        </div>

                        <div class="animate-zen stagger-2 group">
                            <label class="text-[10px] lowercase tracking-[0.4em] font-light text-slate-400 mb-2 block">Stock Volume</label>
                            <div class="flex items-baseline gap-2">
                                <h3 class="text-4xl font-black tracking-tighter italic">8,540</h3>
                            </div>
                            <div class="w-full h-[0.5px] bg-slate-200 dark:bg-white/10 mt-6 group-hover:bg-zen-blue transition-colors"></div>
                        </div>

                        <div class="animate-zen stagger-3 group">
                            <label class="text-[10px] lowercase tracking-[0.4em] font-light text-slate-400 mb-2 block">Net Value</label>
                            <div class="flex items-baseline gap-2">
                                <h3 class="text-4xl font-black tracking-tighter italic">Rp 245M</h3>
                            </div>
                            <div class="w-full h-[0.5px] bg-slate-200 dark:bg-white/10 mt-6 group-hover:bg-zen-blue transition-colors"></div>
                        </div>

                        <div class="animate-zen stagger-4 group">
                            <label class="text-[10px] lowercase tracking-[0.4em] font-light text-slate-400 mb-2 block">Alerts</label>
                            <div class="flex items-baseline gap-2 text-rose-500">
                                <h3 class="text-4xl font-black tracking-tighter italic">12</h3>
                            </div>
                            <div class="w-full h-[0.5px] bg-rose-500/20 mt-6 group-hover:bg-rose-500 transition-colors"></div>
                        </div>
                    </div>

                    <!-- Main Grid -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                        <!-- Chart Area -->
                        <div class="lg:col-span-2 space-y-8 animate-zen stagger-2">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xl font-black tracking-tight italic">Performance Trends</h3>
                                <div class="flex gap-6">
                                    <div class="flex items-center gap-2">
                                        <div class="w-1.5 h-1.5 rounded-full bg-zen-blue"></div>
                                        <span class="text-[9px] lowercase tracking-widest font-light">incoming</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-1.5 h-1.5 rounded-full bg-slate-400"></div>
                                        <span class="text-[9px] lowercase tracking-widest font-light">outgoing</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Zen Glass Chart Container -->
                            <div class="h-80 w-full zen-glass rounded-zen border border-slate-200/50 dark:border-white/5 p-10 flex items-end justify-between gap-2">
                                <!-- Minimalist Bar Chart Illustration -->
                                @for($i = 0; $i < 20; $i++)
                                <div class="flex-1 flex flex-col gap-1 items-center group cursor-pointer">
                                    <div class="w-1.5 bg-zen-blue/20 rounded-full h-32 relative overflow-hidden group-hover:h-40 transition-all duration-500">
                                        <div class="absolute bottom-0 left-0 w-full bg-zen-blue rounded-full" style="height: {{ rand(20, 80) }}%"></div>
                                    </div>
                                    <span class="text-[8px] font-light text-slate-500 opacity-0 group-hover:opacity-100 transition-opacity">0{{ $i }}</span>
                                </div>
                                @endfor
                            </div>
                        </div>

                        <!-- Side List -->
                        <div class="space-y-8 animate-zen stagger-3">
                            <h3 class="text-xl font-black tracking-tight italic">Recent Pulse</h3>
                            <div class="space-y-2">
                                @foreach($recentTransactions as $tx)
                                <div class="zen-glass zen-card-hover p-6 rounded-zen flex items-center gap-5 border border-slate-200/50 dark:border-white/5">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $tx->type === 'in' ? 'bg-emerald-500/10 text-emerald-500' : 'bg-rose-500/10 text-rose-500' }}">
                                        <i data-lucide="{{ $tx->type === 'in' ? 'arrow-down-left' : 'arrow-up-right' }}" class="w-4 h-4" stroke-width="1.5"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold tracking-tight truncate">{{ $tx->item->nama_barang }}</p>
                                        <p class="text-[9px] lowercase tracking-widest text-slate-500 mt-1">{{ $tx->created_at->diffForHumans() }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-black {{ $tx->type === 'in' ? 'text-emerald-500' : 'text-rose-500' }}">
                                            {{ $tx->type === 'in' ? '+' : '-' }}{{ $tx->quantity }}
                                        </p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <button class="w-full py-4 text-[10px] lowercase tracking-[0.4em] font-light text-slate-400 border border-slate-200/50 dark:border-white/5 rounded-zen hover:bg-slate-500/5 hover:text-slate-200 transition-all">
                                View Full Intelligence
                            </button>
                        </div>
                    </div>

                    <!-- Dynamic Widgets Section -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 pt-12 border-t border-slate-200/50 dark:border-white/5">
                        <div class="animate-zen stagger-4">
                            <h4 class="text-lg font-black tracking-tight mb-8 italic">Strategic Supply</h4>
                            <div class="zen-glass p-10 rounded-zen relative overflow-hidden group">
                                <i data-lucide="brain-circuit" class="absolute -right-10 -bottom-10 w-48 h-48 text-zen-blue/5 group-hover:scale-110 transition-transform duration-1000"></i>
                                <p class="text-sm font-light text-slate-400 mb-8 leading-relaxed">Leverage predictive analytics to anticipate stockouts before they happen.</p>
                                <a href="#" class="text-[10px] lowercase tracking-[0.3em] font-bold text-zen-blue flex items-center gap-2 group">
                                    Initialize Forecast
                                    <i data-lucide="chevron-right" class="w-3 h-3 group-hover:translate-x-1 transition-transform"></i>
                                </a>
                            </div>
                        </div>
                        <div class="animate-zen stagger-4">
                            <h4 class="text-lg font-black tracking-tight mb-8 italic">Asset Velocity</h4>
                            <div class="zen-glass p-10 rounded-zen relative overflow-hidden group">
                                <i data-lucide="zap" class="absolute -right-10 -bottom-10 w-48 h-48 text-amber-500/5 group-hover:scale-110 transition-transform duration-1000"></i>
                                <p class="text-sm font-light text-slate-400 mb-8 leading-relaxed">Monitor real-time movement and turnover rates across all warehouses.</p>
                                <a href="#" class="text-[10px] lowercase tracking-[0.3em] font-bold text-slate-200 flex items-center gap-2 group">
                                    Velocity Map
                                    <i data-lucide="chevron-right" class="w-3 h-3 group-hover:translate-x-1 transition-transform"></i>
                                </a>
                            </div>
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
