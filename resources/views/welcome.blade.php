<!DOCTYPE html>
<html lang="en" x-data="{ 
    darkMode: localStorage.getItem('theme') === 'dark',
    scrolled: false,
    scrollProgress: 0,
    audioContext: null,
    playFeedback(type = 'click') {
        if (!this.audioContext) this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const osc = this.audioContext.createOscillator();
        const gain = this.audioContext.createGain();
        osc.connect(gain); gain.connect(this.audioContext.destination);
        if (type === 'click') {
            osc.frequency.setValueAtTime(800, this.audioContext.currentTime);
            osc.frequency.exponentialRampToValueAtTime(1200, this.audioContext.currentTime + 0.05);
            gain.gain.setValueAtTime(0.05, this.audioContext.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, this.audioContext.currentTime + 0.1);
            osc.start(); osc.stop(this.audioContext.currentTime + 0.1);
        } else if (type === 'hover') {
            osc.frequency.setValueAtTime(400, this.audioContext.currentTime);
            gain.gain.setValueAtTime(0.01, this.audioContext.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, this.audioContext.currentTime + 0.05);
            osc.start(); osc.stop(this.audioContext.currentTime + 0.05);
        }
    }
}" :class="{ 'dark': darkMode }" @scroll.window="scrolled = (window.pageYOffset > 50); scrollProgress = (window.pageYOffset / (document.documentElement.scrollHeight - window.innerHeight)) * 100">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>StockMaster | Infinite Intelligence Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: { slate: { 950: '#020617' } },
                    animation: { 
                        'drift': 'drift 25s infinite linear',
                        'float-slow': 'float 6s ease-in-out infinite'
                    },
                    keyframes: {
                        drift: { '0%': { transform: 'translate(0, 0)' }, '50%': { transform: 'translate(80px, 40px)' }, '100%': { transform: 'translate(0, 0)' } },
                        float: { '0%, 100%': { transform: 'translateY(0)' }, '50%': { transform: 'translateY(-20px)' } }
                    }
                }
            }
        }
    </script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200;400;600;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Manrope', sans-serif; background-color: #fcfcfc; transition: background-color 0.5s ease; color: #0f172a; overflow-x: hidden; scroll-behavior: smooth; }
        .dark body { background-color: #020617; color: #f8fafc; }

        .crystal-glass {
            background: rgba(248, 250, 252, 0.98);
            backdrop-filter: blur(40px);
            border: 1.5px solid rgba(15, 23, 42, 0.12);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
            transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .dark .crystal-glass {
            background: rgba(30, 41, 59, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 30px 60px rgba(0,0,0,0.5);
        }

        .scroll-reveal {
            opacity: 0;
            transform: translateY(40px);
            transition: all 1s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .scroll-reveal.active { opacity: 1; transform: translateY(0); }

        .tilt-card { transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1); transform-style: preserve-3d; }

        .glass-orb {
            position: absolute; border-radius: 50%;
            background: radial-gradient(circle at 30% 30%, rgba(16, 185, 129, 0.08), transparent);
            filter: blur(50px); pointer-events: none; z-index: 0;
        }

        .terminal-log { font-family: 'monospace'; font-size: 10px; color: #10b981; height: 350px; overflow: hidden; }

        .progress-bar {
            position: fixed; top: 0; left: 0; height: 3px;
            background: linear-gradient(to right, #10b981, #3b82f6);
            z-index: 1000; transition: width 0.1s ease-out;
        }

        .magnetic-wrap { transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1); }

        [x-cloak] { display: none !important; }
    </style>
</head>
<body @mousemove="x = $event.clientX; y = $event.clientY" x-data="{ x: 0, y: 0 }">

    <!-- Scroll Progress -->
    <div class="progress-bar" :style="'width: ' + scrollProgress + '%'"></div>

    <!-- Background Elements -->
    <div class="glass-orb w-[600px] h-[600px] -top-40 -right-40 animate-drift"></div>
    <div class="glass-orb w-[400px] h-[400px] top-[40%] -left-40 animate-drift" style="animation-delay: -7s; background: radial-gradient(circle at 30% 30%, rgba(59, 130, 246, 0.06), transparent);"></div>

    <!-- Navigation -->
    <header class="fixed top-0 left-0 right-0 z-50 transition-all duration-500" :class="scrolled ? 'py-4' : 'py-8'">
        <nav class="max-w-7xl mx-auto px-6">
            <div class="crystal-glass rounded-3xl flex justify-between items-center px-10 h-16 shadow-2xl">
                <div class="flex items-center gap-3 cursor-pointer" @click="window.scrollTo(0,0); playFeedback()">
                    <div class="w-8 h-8 bg-[#0f172a] dark:bg-white rounded-xl flex items-center justify-center shadow-md">
                        <i data-lucide="shield-check" class="w-4 h-4 text-white dark:text-[#0f172a]"></i>
                    </div>
                    <span class="font-extrabold text-lg tracking-tighter uppercase italic text-[#0f172a] dark:text-white">StockMaster</span>
                </div>
                <div class="hidden md:flex items-center gap-10">
                    <a href="#workflow" class="text-[10px] font-bold uppercase tracking-[0.4em] text-slate-500 hover:text-emerald-500 transition-all">Workflow</a>
                    <a href="#features" class="text-[10px] font-bold uppercase tracking-[0.4em] text-slate-500 hover:text-emerald-500 transition-all">Intelligence</a>
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('login') }}" class="bg-[#0f172a] dark:bg-white text-white dark:text-[#0f172a] px-8 py-2.5 rounded-xl font-bold text-[9px] uppercase tracking-widest shadow-xl hover:scale-105 transition-all" @click="playFeedback()">Initialize</a>
                    <button @click="darkMode = !darkMode; localStorage.setItem('theme', darkMode ? 'dark' : 'light'); playFeedback()" class="w-10 h-10 rounded-xl bg-slate-200 dark:bg-white/5 flex items-center justify-center">
                        <i data-lucide="sun" x-show="darkMode" class="w-4 h-4 text-white"></i>
                        <i data-lucide="moon" x-show="!darkMode" class="w-4 h-4 text-[#0f172a]"></i>
                    </button>
                </div>
            </div>
        </nav>
    </header>

    <main class="relative z-10">
        <!-- Hero Section -->
        <section class="min-h-screen flex items-center pt-24 px-6 relative">
            <div class="max-w-7xl mx-auto w-full grid grid-cols-1 lg:grid-cols-12 gap-20 items-center">
                <div class="lg:col-span-6 space-y-10">
                    <div class="inline-flex items-center gap-3 px-5 py-2.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 scroll-reveal active animate-float-slow">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-[10px] font-extrabold uppercase tracking-[0.4em] text-emerald-600 italic">Synaptic Active</span>
                    </div>
                    <h1 class="text-7xl md:text-[90px] font-extrabold leading-[0.85] italic uppercase tracking-tighter text-[#0f172a] dark:text-white scroll-reveal active">
                        Infinite<br/>Intelligence<br/>Awaits
                    </h1>
                    <p class="text-xl text-slate-600 dark:text-slate-400 max-w-lg font-medium leading-relaxed scroll-reveal active">
                        Zero-latency inventory engine for modern enterprise hubs. Predictive depletion mapping and autonomous reconciliation.
                    </p>
                    <div class="flex items-center gap-10 scroll-reveal active">
                        <a href="{{ route('login') }}" class="magnetic-wrap bg-[#0f172a] dark:bg-white text-white dark:text-[#0f172a] px-16 py-6 rounded-2xl font-extrabold text-[11px] uppercase tracking-[0.3em] italic shadow-2xl hover:-translate-y-2 transition-all" @click="playFeedback()">Initialize Console</a>
                        <div class="text-right">
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Global Status</p>
                            <p class="text-3xl font-extrabold italic text-emerald-500 animate-pulse">STABLE</p>
                        </div>
                    </div>
                </div>
                <div class="lg:col-span-6 relative scroll-reveal active">
                    <div class="crystal-glass p-3 rounded-[4.5rem] relative tilt-card shadow-2xl">
                        <div class="bg-slate-950 rounded-[4rem] p-12 h-[500px] relative overflow-hidden shadow-inner">
                            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-emerald-500 to-transparent opacity-50 animate-pulse"></div>
                            <div class="flex justify-between items-center mb-10">
                                <div class="flex gap-2.5">
                                    <div class="w-3.5 h-3.5 rounded-full bg-red-500/60 shadow-lg"></div>
                                    <div class="w-3.5 h-3.5 rounded-full bg-amber-500/60 shadow-lg"></div>
                                    <div class="w-3.5 h-3.5 rounded-full bg-emerald-500/60 shadow-lg"></div>
                                </div>
                                <div class="text-[10px] font-bold text-emerald-500/80 uppercase tracking-[0.2em]">Synaptic_Console_v12</div>
                            </div>
                            <div class="terminal-log space-y-6" id="terminal-content">
                                <p class="opacity-50">> BOOT_SEQUENCE_COMPLETE...</p>
                                <p>> SCANNING_NODES... [OK]</p>
                                <p class="text-white bg-emerald-500/20 px-2 rounded inline-block">> ALERT: SYSTEM_OPTIMIZED_FOR_LATENCY</p>
                            </div>
                        </div>
                        <!-- Floating Engine Load -->
                        <div class="absolute -bottom-12 -left-12 crystal-glass p-10 rounded-[4rem] shadow-2xl animate-float-slow border-emerald-500/20 z-20 transition-transform hover:scale-110">
                            <i data-lucide="brain-circuit" class="w-14 h-14 text-emerald-500 mb-4"></i>
                            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Engine Load</p>
                            <p class="text-4xl font-extrabold italic text-[#0f172a] dark:text-white">1.2%</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Strategic Workflow Journey -->
        <section id="workflow" class="py-56 bg-slate-50 dark:bg-slate-950 transition-colors relative overflow-hidden">
            <div class="max-w-7xl mx-auto px-6 relative z-10">
                <div class="text-center mb-40 scroll-reveal">
                    <h2 class="text-6xl font-extrabold italic uppercase tracking-tighter mb-6 text-[#0f172a] dark:text-white">Strategic Workflow</h2>
                    <div class="w-24 h-2 bg-emerald-500 mx-auto rounded-full mb-8"></div>
                    <p class="text-[11px] font-extrabold uppercase tracking-[0.6em] text-slate-400">The StockMaster Evolution Journey</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-24 relative">
                    <div class="hidden md:block absolute top-32 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-emerald-500/40 to-transparent"></div>
                    
                    @foreach([
                        ['scan', 'Asset Capture', 'High-fidelity SKU recognition and automated metadata mapping across nodes.'],
                        ['cpu', 'Synaptic Engine', 'Dual-node intelligence evaluates turnover dynamics and seasonal risk factors.'],
                        ['database', 'Global Sync', 'Autonomous reconciliation across enterprise ledgers with zero latency.']
                    ] as $index => $step)
                    <div class="relative z-10 flex flex-col items-center text-center space-y-12 scroll-reveal tilt-card" style="transition-delay: {{ $index * 0.3 }}s">
                        <div class="magnetic-wrap w-32 h-32 rounded-[3.5rem] crystal-glass flex items-center justify-center shadow-2xl group cursor-pointer overflow-visible">
                            <i data-lucide="{{ $step[0] }}" class="w-14 h-14 text-emerald-500 group-hover:rotate-[360deg] transition-transform duration-[1.5s]"></i>
                            <div class="absolute -top-5 -right-5 w-12 h-12 rounded-3xl bg-[#0f172a] text-white flex items-center justify-center font-black text-lg shadow-2xl border-4 border-emerald-500/20">0{{ $index + 1 }}</div>
                        </div>
                        <div class="space-y-6">
                            <h3 class="text-4xl font-extrabold italic uppercase tracking-tighter text-[#0f172a] dark:text-white">{{ $step[1] }}</h3>
                            <p class="text-base text-slate-600 dark:text-slate-400 leading-relaxed max-w-xs mx-auto">{{ $step[2] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Features Hub -->
        <section id="features" class="py-56 bg-white dark:bg-[#020617] transition-colors relative overflow-hidden">
            <div class="max-w-7xl mx-auto px-6 relative z-10">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-16">
                    <div class="md:col-span-8 crystal-glass p-20 rounded-[6rem] tilt-card scroll-reveal">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-20">
                            <div class="space-y-12">
                                <div class="w-20 h-20 rounded-3xl bg-[#0f172a] dark:bg-white flex items-center justify-center shadow-2xl group cursor-pointer magnetic-wrap">
                                    <i data-lucide="users" class="w-10 h-10 text-white dark:text-[#0f172a]"></i>
                                </div>
                                <h3 class="text-5xl font-extrabold italic uppercase tracking-tighter text-[#0f172a] dark:text-white">Supplier Intel</h3>
                                <p class="text-lg text-slate-600 dark:text-slate-400 leading-relaxed">Integrated supplier performance metrics. Analyze lead times, reliability, and cost-flux in a single synaptic view.</p>
                                <div class="flex gap-4">
                                    @foreach([1,2,3] as $p)
                                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center animate-pulse" style="animation-delay: {{ $p * 0.2 }}s">
                                        <i data-lucide="shield" class="w-6 h-6 text-emerald-500"></i>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="bg-slate-50 dark:bg-white/5 rounded-[5rem] p-12 flex flex-col justify-center gap-10 border border-slate-200 dark:border-white/10 shadow-inner group hover:border-emerald-500/30 transition-colors">
                                <div class="space-y-2">
                                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Master Partner</p>
                                    <p class="text-3xl font-extrabold italic text-emerald-500">Zenith_Global</p>
                                </div>
                                <div class="space-y-2">
                                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Reliability Index</p>
                                    <p class="text-5xl font-extrabold italic text-[#0f172a] dark:text-white">99.8%</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="md:col-span-4 crystal-glass p-16 rounded-[6rem] tilt-card scroll-reveal flex flex-col justify-between" style="transition-delay: 0.2s">
                        <div>
                            <i data-lucide="bar-chart-big" class="w-16 h-16 text-emerald-500 mb-12 animate-pulse"></i>
                            <h3 class="text-4xl font-extrabold italic uppercase tracking-tighter text-[#0f172a] dark:text-white mb-8">Global Audit</h3>
                            <p class="text-base text-slate-600 dark:text-slate-400 leading-relaxed">High-fidelity financial reporting and autonomous inventory valuations. Perfectly calculated and tax-ready.</p>
                        </div>
                        <div class="pt-10 border-t border-slate-200 dark:border-white/10">
                            <span class="text-[10px] font-black uppercase tracking-[0.5em] text-emerald-500 italic">Zero Latency Mode</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Master Hub -->
        <section class="py-56 px-6 relative overflow-hidden">
            <div class="max-w-6xl mx-auto crystal-glass p-32 md:p-48 rounded-[8rem] text-center scroll-reveal tilt-card relative z-10 overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-b from-emerald-500/20 via-transparent to-transparent animate-pulse"></div>
                <h2 class="text-7xl md:text-[120px] font-extrabold italic uppercase tracking-tighter text-[#0f172a] dark:text-white mb-24 relative z-10 leading-[0.8]">Ignite Your<br/>Ecosystem</h2>
                <div class="flex flex-col md:flex-row justify-center gap-12 relative z-10">
                    <a href="{{ route('login') }}" class="magnetic-wrap bg-[#0f172a] dark:bg-white text-white dark:text-[#0f172a] px-28 py-9 rounded-[2.5rem] font-extrabold text-sm uppercase tracking-[0.4em] shadow-2xl hover:scale-110 transition-all relative z-10" @click="playFeedback()">Initialize</a>
                </div>
                <p class="mt-24 text-[11px] font-extrabold uppercase tracking-[0.8em] text-slate-400 relative z-10">StockMaster Protocol • High-Fidelity ERP • v12.4.0</p>
            </div>
        </section>
    </main>

    <footer class="py-40 border-t border-slate-200 dark:border-white/5 px-6 bg-white dark:bg-[#020617] transition-colors relative z-10">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-20">
            <div class="flex items-center gap-6 group cursor-pointer" @click="window.scrollTo(0,0)">
                <div class="w-16 h-16 bg-[#0f172a] dark:bg-white rounded-2xl flex items-center justify-center shadow-2xl group-hover:rotate-[360deg] transition-transform duration-1000">
                    <i data-lucide="shield-check" class="w-8 h-8 text-white dark:text-[#0f172a]"></i>
                </div>
                <span class="font-extrabold text-4xl tracking-tighter uppercase italic text-[#0f172a] dark:text-white">StockMaster</span>
            </div>
            <p class="text-[10px] font-bold text-slate-300 uppercase tracking-widest italic">© 2026 Zenith Perfection Protocol</p>
        </div>
    </footer>

    <script>
        lucide.createIcons();
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => { if (entry.isIntersecting) entry.target.classList.add('active'); });
        }, { threshold: 0.1 });
        document.querySelectorAll('.scroll-reveal').forEach(el => observer.observe(el));

        const terminalContent = document.getElementById('terminal-content');
        const logs = ['> AUDITING...', '> SYNCING...', '> OPTIMIZING...', '> DONE.', '> MONITORING...'];
        let logIndex = 0;
        setInterval(() => {
            const p = document.createElement('p');
            p.innerText = logs[logIndex];
            p.className = 'opacity-0 translate-x-4 transition-all duration-500';
            terminalContent.appendChild(p);
            setTimeout(() => { p.className = 'opacity-100 translate-x-0'; }, 50);
            if (terminalContent.children.length > 12) terminalContent.removeChild(terminalContent.firstChild);
            logIndex = (logIndex + 1) % logs.length;
        }, 2200);

        // 3D Tilt Card Logic
        document.querySelectorAll('.tilt-card').forEach(card => {
            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = (e.clientY - rect.top - rect.height/2) / 35;
                const y = (rect.width/2 - (e.clientX - rect.left)) / 35;
                card.style.transform = `perspective(1000px) rotateX(${x}deg) rotateY(${y}deg) translateY(-15px) scale(1.02)`;
            });
            card.addEventListener('mouseleave', () => { card.style.transform = `perspective(1000px) rotateX(0) rotateY(0) translateY(0) scale(1)`; });
        });

        // Independent Magnetic Effect Logic
        document.querySelectorAll('.magnetic-wrap').forEach(btn => {
            btn.addEventListener('mousemove', (e) => {
                const rect = btn.getBoundingClientRect();
                const x = (e.clientX - rect.left - rect.width/2) * 0.2;
                const y = (e.clientY - rect.top - rect.height/2) * 0.2;
                btn.style.transform = `translate(${x}px, ${y}px)`;
            });
            btn.addEventListener('mouseleave', () => { btn.style.transform = `translate(0, 0)`; });
        });
    </script>
</body>
</html>
