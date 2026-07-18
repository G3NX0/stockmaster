<!DOCTYPE html>
<html lang="en" class="h-full" x-data="{ 
    darkMode: localStorage.getItem('theme') === 'dark', 
    page: 'dashboard', 
    sheetOpen: false, 
    commandPaletteOpen: false,
    intelOpen: false,
    commandQuery: '',
    sidebarGroups: { 
        protocol: true, 
        intelligence: true
    },
    themeSweeping: false,
    commands: [
        { name: 'Dashboard Protocol', icon: 'layout-grid', route: '{{ route('dashboard') }}' },
        { name: 'Profit Analytics', icon: 'pie-chart', route: '{{ route('reports.profit') }}' }
    ],
    get filteredCommands() {
        if (!this.commandQuery) return this.commands;
        return this.commands.filter(c => c.name.toLowerCase().includes(this.commandQuery.toLowerCase()));
    },
    // Synaptic AI State
    aiQuery: '',
    aiLoading: false,
    mobileMenuOpen: false, // For Dock expanded states if needed
    aiMessages: [
        { role: 'ai', text: 'Protocol initialized. How can I assist with your inventory today?', time: '{{ now()->format('H:i') }}' }
    ],
    // Cursor Spotlight State
    cursor: { x: 0, y: 0 },
    updateCursor(e) {
        this.cursor.x = e.clientX;
        this.cursor.y = e.clientY;
    },
    createRipple(e) {
        const ripple = document.createElement('div');
        ripple.className = 'ripple';
        const size = 150;
        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = e.clientX - size/2 + 'px';
        ripple.style.top = e.clientY - size/2 + 'px';
        document.body.appendChild(ripple);
        setTimeout(() => ripple.remove(), 800);
        
        // Smart Audio Collision: Only play ripple sound if NOT clicking an interactive element
        if (!e.target.closest('button, a, input, select, textarea, [role=button]')) {
            this.playFeedback();
        }
    },
    async sendAiMessage() {
        if (!this.aiQuery.trim() || this.aiLoading) return;
        
        const userMsg = this.aiQuery;
        this.aiMessages.push({ role: 'user', text: userMsg, time: new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) });
        this.aiQuery = '';
        this.aiLoading = true;
        this.playFeedback();

        try {
            const response = await fetch('{{ route('ai.chat') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message: userMsg })
            });
            const data = await response.json();
            this.aiMessages.push({ role: 'ai', text: data.message, time: data.timestamp });
            this.playFeedback('success');
        } catch (e) {
            this.aiMessages.push({ role: 'ai', text: 'Connection to Synapse interrupted. Please retry.', time: 'ERROR' });
        } finally {
            this.aiLoading = false;
            this.$nextTick(() => {
                const container = this.$refs.chatContainer;
                if (container) container.scrollTop = container.scrollHeight;
            });
        }
    },
    toggleGroup(group) {
        if (this.sidebarGroups[group]) {
            this.sidebarGroups[group] = false;
        } else {
            Object.keys(this.sidebarGroups).forEach(k => this.sidebarGroups[k] = false);
            this.sidebarGroups[group] = true;
        }
        this.playFeedback();
    },
    toggleDarkMode() {
        this.themeSweeping = true;
        this.playFeedback('success');
        
        setTimeout(() => {
            this.darkMode = !this.darkMode;
            localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
            if(typeof initChart === 'function') initChart(this.darkMode);
        }, 500); 

        setTimeout(() => {
            this.themeSweeping = false;
        }, 1300);
    },
    toast: { show: false, message: '', type: 'success' },
    playFeedback(type = 'click') {
        if (window.playFeedback) window.playFeedback(type);
    },
    showToast(msg, type = 'success') {
        this.playFeedback(type === 'success' ? 'success' : 'click');
        this.toast.message = msg;
        this.toast.type = type;
        this.toast.show = true;
        setTimeout(() => this.toast.show = false, 3000);
    }
}" :class="{ 'dark': darkMode }" 
   @keydown.window.ctrl.k.prevent="commandPaletteOpen = !commandPaletteOpen; if(commandPaletteOpen) $nextTick(() => $refs.commandInput.focus())"
   x-on:show-toast.window="showToast($event.detail.message, $event.detail.type)"
   x-effect="if(typeof initChart === 'function') initChart(darkMode)">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('page_title', config('app.name', 'StockMaster'))</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#080808">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;300;400;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
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
        html { background-color: #fcfcfc; scroll-behavior: smooth; }
        html.dark { background-color: #000000; }
        body { font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; background-color: transparent !important; }
        .dark body { background-color: transparent !important; }
        
        .zen-glass {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(40px) saturate(200%);
            -webkit-backdrop-filter: blur(40px) saturate(200%);
            border: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: 0 8px 32px -4px rgba(0, 0, 0, 0.05);
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

        /* Custom scrollbar utility (Light Mode default) */
        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: rgba(0, 0, 0, 0.15) transparent;
        }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(0, 0, 0, 0.15); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(0, 0, 0, 0.3); }

        /* Custom scrollbar utility (Dark Mode overrides) */
        .dark .custom-scrollbar {
            scrollbar-color: rgba(255, 255, 255, 0.15) transparent;
        }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.15); }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.3); }

        /* Chat Scrollbar - WhatsApp Emerald Theme (Light Mode default) */
        .chat-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: rgba(16, 185, 129, 0.4) transparent;
        }
        .chat-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .chat-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .chat-scrollbar::-webkit-scrollbar-thumb { background: rgba(16, 185, 129, 0.4); border-radius: 10px; transition: background 0.3s; }
        .chat-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(16, 185, 129, 0.65); }

        /* Chat Scrollbar - WhatsApp Emerald Theme (Dark Mode overrides) */
        .dark .chat-scrollbar {
            scrollbar-color: rgba(16, 185, 129, 0.35) transparent;
        }
        .dark .chat-scrollbar::-webkit-scrollbar-thumb { background: rgba(16, 185, 129, 0.35); }
        .dark .chat-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(16, 185, 129, 0.6); }

        /* Global scrollbars for root layout & all scroll containers (Light Mode default) */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.15);
            border-radius: 100px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.3);
        }

        /* Global scrollbars (Dark Mode overrides) */
        .dark ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
        }
        .dark ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        
        /* Firefox Global */
        html {
            scrollbar-width: thin;
            scrollbar-color: rgba(0, 0, 0, 0.15) transparent;
        }
        html.dark {
            scrollbar-color: rgba(255, 255, 255, 0.15) transparent;
        }

        [x-cloak] { display: none !important; }

        .intelligence-sidebar {
            width: 450px;
            transform: translateX(100%);
            transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .intelligence-sidebar.open { transform: translateX(0); }

        /* Zenith Micro-Dynamics */
        .tilt-card {
            transition: transform 0.5s cubic-bezier(0.23, 1, 0.32, 1), box-shadow 0.5s ease;
            transform-style: preserve-3d;
            perspective: 1000px;
        }
        .tilt-card:hover {
            transform: translateY(-15px) rotateX(10deg) rotateY(5deg);
            box-shadow: 0 40px 80px -15px rgba(0,0,0,0.25);
        }
        .dark .tilt-card:hover {
            box-shadow: 0 40px 80px -15px rgba(0,0,0,0.7);
        }

        .magnetic-btn {
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .magnetic-btn:hover {
            transform: scale(1.1) translateY(-2px);
        }
        .magnetic-btn:active {
            transform: scale(0.95);
        }

        .glow-icon {
            transition: all 0.4s ease;
        }
        .group:hover .glow-icon {
            filter: drop-shadow(0 0 8px currentColor);
            transform: scale(1.1);
        }

        .row-action-trigger .actions {
            opacity: 0;
            transform: translateX(20px);
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .row-action-trigger:hover .actions {
            opacity: 1;
            transform: translateX(0);
        }

        .parallax-icon {
            transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .tilt-card:hover .parallax-icon {
            transform: translateZ(50px) translateX(-20px) translateY(-10px) scale(1.1);
        }

        .cursor-spotlight {
            position: fixed;
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.03) 0%, rgba(0,0,0,0) 70%);
            pointer-events: none;
            z-index: 1;
            transform: translate(-50%, -50%);
            transition: background 1s ease;
        }
        .dark .cursor-spotlight {
            background: radial-gradient(circle, rgba(255, 255, 255, 0.04) 0%, rgba(0,0,0,0) 70%);
        }

        .ripple {
            position: fixed;
            border-radius: 50%;
            background: rgba(128, 128, 128, 0.1);
            transform: scale(0);
            animation: ripple-animation 0.8s ease-out;
            pointer-events: none;
            z-index: 9999;
        }
        .dark .ripple { background: rgba(255, 255, 255, 0.05); }
        @keyframes ripple-animation {
            to { transform: scale(4); opacity: 0; }
        }

        @keyframes ripple-animation {
            to { transform: scale(4); opacity: 0; }
        }

        .boot-overlay {
            position: fixed;
            inset: 0;
            background: #000;
            z-index: 99999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-family: 'Inter', monospace;
        }

        /* --- Zenith Transition Prototypes --- */
        
        /* 1. Neural Wipe */
        .neural-wipe-enter {
            position: fixed;
            inset: 0;
            background: rgba(255, 255, 255, 0.01);
            backdrop-filter: blur(0px);
            z-index: 9000;
            pointer-events: none;
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .neural-wipe-active {
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.1);
        }
        .neural-bar {
            position: fixed;
            top: 0;
            left: 0;
            height: 3px;
            background: linear-gradient(90deg, transparent, #10b981, transparent);
            width: 100%;
            transform: translateX(-100%);
            z-index: 9001;
        }
        .neural-bar-active {
            animation: neural-progress 0.8s ease-in-out infinite;
        }
        @keyframes neural-progress {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        /* 2. Seamless Slide */
        .slide-overlay {
            position: fixed;
            inset: 0;
            background: #000;
            z-index: 9000;
            transform: translateX(100%);
            transition: transform 0.5s cubic-bezier(0.7, 0, 0.3, 1);
            pointer-events: none;
        }
        .slide-active { transform: translateX(0); }

        /* 3. Strobe Pulse */
        .strobe-dim {
            transition: all 0.3s ease;
        }
        .strobe-active {
            filter: brightness(0.5) blur(4px);
            transform: scale(0.98);
        }

        .boot-text {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5em;
            font-weight: 900;
            margin-top: 2rem;
            opacity: 0;
            animation: boot-flicker 0.1s infinite alternate;
        }
        @keyframes boot-flicker {
            from { opacity: 0.5; }
            to { opacity: 1; }
        }

        /* Zenith Super-Dynamics */
        @keyframes neural-drift {
            0% { transform: translate(0, 0) scale(1) rotate(0deg); filter: blur(140px); opacity: 0.4; }
            33% { transform: translate(12%, 8%) scale(1.3) rotate(8deg); filter: blur(180px); opacity: 0.6; }
            66% { transform: translate(-8%, 12%) scale(0.9) rotate(-8deg); filter: blur(120px); opacity: 0.5; }
            100% { transform: translate(0, 0) scale(1) rotate(0deg); filter: blur(140px); opacity: 0.4; }
        }
        .neural-orb {
            position: fixed; border-radius: 50%;
            z-index: -1; pointer-events: none;
            animation: neural-drift 25s infinite ease-in-out;
            will-change: transform, filter;
        }

        @keyframes light-sweep {
            0% { transform: translateX(-120%) skewX(-15deg); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateX(300%) skewX(-15deg); opacity: 0; }
        }
        .sweep-effect {
            position: fixed; inset: 0; z-index: 9999;
            pointer-events: none; opacity: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.9), transparent);
            box-shadow: 0 0 100px rgba(255,255,255,0.4);
            backdrop-filter: blur(12px) brightness(1.5);
            -webkit-backdrop-filter: blur(12px) brightness(1.5);
        }
        .sweep-active { animation: light-sweep 1.2s ease-in-out forwards; }

        .skeleton-shimmer {
            background: linear-gradient(90deg, rgba(0,0,0,0.05) 25%, rgba(0,0,0,0.1) 50%, rgba(0,0,0,0.05) 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }
        .dark .skeleton-shimmer {
            background: linear-gradient(90deg, rgba(255,255,255,0.02) 25%, rgba(255,255,255,0.05) 50%, rgba(255,255,255,0.02) 75%);
            background-size: 200% 100%;
        }
        @keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }

        .critical-glow {
            box-shadow: 0 0 20px rgba(244, 63, 94, 0.2);
            animation: critical-pulse 2s infinite;
        }
        @keyframes critical-pulse {
            0%, 100% { box-shadow: 0 0 15px rgba(244, 63, 94, 0.2); }
            50% { box-shadow: 0 0 35px rgba(244, 63, 94, 0.5); }
        }

        /* Zenith New Dynamics */
        .holographic-card {
            transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.2s;
            transform-style: preserve-3d;
            position: relative;
            overflow: hidden;
        }
        .holographic-card:hover { transform: translateY(-10px) scale(1.02); }
        .holographic-card:hover::after { 
            transform: translateX(var(--glare-x, 0)) translateY(var(--glare-y, 0)); 
            opacity: 1;
        }
        .holographic-card::after {
            content: '';
            position: absolute; inset: -100%;
            background: radial-gradient(circle at center, rgba(255,255,255,0.2) 0%, transparent 60%);
            opacity: 0;
            transition: opacity 0.3s;
            pointer-events: none;
        }

        .heartbeat-pulse {
            animation: zenith-heartbeat 1.5s ease-in-out infinite;
            display: inline-block;
        }
        @keyframes zenith-heartbeat {
            0%, 100% { transform: scale(1); filter: brightness(1); }
            50% { transform: scale(1.1); filter: brightness(1.3); text-shadow: 0 0 10px currentColor; }
        }

        .liquid-container {
            position: relative; overflow: hidden; height: 100%; width: 100%;
        }
        .liquid-wave {
            position: absolute; bottom: 0; left: 0; width: 200%; height: 100%;
            background: rgba(16, 185, 129, 0.4);
            animation: liquid-rise 5s ease-in-out infinite alternate, wave-move 10s linear infinite;
            transform-origin: bottom;
            border-radius: 40% 45% 42% 40%;
        }
        @keyframes wave-move {
            0% { transform: translateX(0) rotate(0deg); }
            100% { transform: translateX(-50%) rotate(360deg); }
        }
        @keyframes liquid-rise {
            from { height: var(--rise-start, 40%); }
            to { height: var(--rise-end, 60%); }
        }

        @keyframes zenith-shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        .zenith-shimmer {
            background: linear-gradient(90deg, 
                transparent 25%, 
                rgba(255, 255, 255, 0.05) 50%, 
                transparent 75%
            );
            background-size: 200% 100%;
            animation: zenith-shimmer 2s infinite linear;
        }
        .dark .zenith-shimmer {
            background: linear-gradient(90deg, 
                transparent 25%, 
                rgba(255, 255, 255, 0.08) 50%, 
                transparent 75%
            );
            background-size: 200% 100%;
        }

        @stack('styles')
    </style>
</head>
<body class="h-full bg-onyx-50 dark:bg-onyx-950 text-onyx-950 dark:text-white antialiased transition-colors duration-500 overflow-hidden" 
      @mousemove="updateCursor" 
      @click="createRipple">
    
    <!-- Neural Pulse Background -->
    <div class="fixed inset-0 z-[-2] overflow-hidden pointer-events-none">
        <div class="neural-orb w-[1000px] h-[1000px] bg-emerald-500/15 top-[-20%] left-[-20%]"></div>
        <div class="neural-orb w-[800px] h-[800px] bg-blue-600/15 bottom-[-10%] right-[-10%]" style="animation-delay: -7s"></div>
        <div class="neural-orb w-[600px] h-[600px] bg-indigo-500/10 top-[30%] left-[10%]" style="animation-delay: -14s"></div>
    </div>

    <!-- Theme Sweep Effect -->
    <div class="sweep-effect" :class="{ 'sweep-active': themeSweeping }"></div>
    
    <!-- Zenith Boot Sequence -->
    <div x-data="{ booting: !sessionStorage.getItem('booted') }" 
         x-init="if(booting) { setTimeout(() => { booting = false; sessionStorage.setItem('booted', 'true'); }, 2000) }"
         x-show="booting"
         x-transition:leave="transition ease-in duration-700"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0 scale-110"
         class="boot-overlay" x-cloak>
        <div class="w-16 h-16 rounded-2xl bg-white flex items-center justify-center animate-pulse">
            <i data-lucide="shield-check" class="w-8 h-8 text-black"></i>
        </div>
        <div class="boot-text">Zenith Protocol Initializing...</div>
        <div class="mt-4 flex gap-1">
            <div class="w-1 h-1 bg-white rounded-full animate-bounce"></div>
            <div class="w-1 h-1 bg-white rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
            <div class="w-1 h-1 bg-white rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
        </div>
    </div>

    <!-- Cursor Spotlight -->
    <div class="cursor-spotlight" :style="'left: ' + cursor.x + 'px; top: ' + cursor.y + 'px;'"></div>

    <div x-init="
        @if(session('success')) showToast('{{ session('success') }}', 'success') @endif
        @if(session('error')) showToast('{{ session('error') }}', 'error') @endif
    "></div>

    <!-- Toast System -->
    <template x-if="true">
        <div x-show="toast.show" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-10"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="fixed bottom-32 left-1/2 -translate-x-1/2 z-[200] px-6 py-4 rounded-2xl zen-glass border border-white/20 flex items-center gap-3 shadow-2xl"
             style="display: none;">
            <div class="w-2.5 h-2.5 rounded-full" :class="toast.type === 'success' ? 'bg-emerald-500' : 'bg-rose-500'"></div>
            <p class="text-xs font-black uppercase tracking-widest" x-text="toast.message"></p>
        </div>
    </template>

    <div class="flex h-full p-4 lg:p-6 gap-6">
        <!-- Zenith Sidebar (Desktop) -->
        <aside class="w-80 hidden xl:flex flex-col rounded-[2.5rem] zen-glass p-8 space-y-10 overflow-y-auto custom-scrollbar">
            <div class="flex items-center gap-5 px-2">
                <div class="w-12 h-12 flex-shrink-0 aspect-square rounded-2xl bg-onyx-950 dark:bg-white flex items-center justify-center shadow-2xl">
                    <i data-lucide="shield-check" class="w-6 h-6 text-white dark:text-black"></i>
                </div>
                <h1 class="text-xl font-black tracking-tighter uppercase italic leading-none">StockMaster</h1>
            </div>

            <nav class="space-y-6 flex-grow">
                <!-- Group 1: Core Protocols -->
                <div class="space-y-2">
                    <p class="text-[9px] font-black tracking-[0.2em] text-onyx-400 uppercase px-6">Core Protocols</p>
                    <div class="space-y-1">
                        <a href="{{ route('dashboard') }}" 
                           class="w-full flex items-center gap-5 px-6 py-3.5 rounded-[1.25rem] transition-all duration-300 {{ request()->routeIs('dashboard') ? 'bg-onyx-950 dark:bg-white text-white dark:text-black shadow-2xl scale-105' : 'text-onyx-500 hover:bg-white/5 hover:translate-x-2' }}">
                            <i data-lucide="layout-grid" class="w-5 h-5"></i>
                            <span class="text-xs font-black tracking-tight">Dashboard</span>
                        </a>
                        <a href="{{ route('items.index') }}" 
                           class="w-full flex items-center gap-5 px-6 py-3.5 rounded-[1.25rem] transition-all duration-300 {{ request()->routeIs('items.*') ? 'bg-onyx-950 dark:bg-white text-white dark:text-black shadow-2xl scale-105' : 'text-onyx-500 hover:bg-white/5 hover:translate-x-2' }}">
                            <i data-lucide="package" class="w-5 h-5"></i>
                            <span class="text-xs font-black tracking-tight">Inventory Registry</span>
                        </a>
                        <a href="{{ route('transactions.index') }}" 
                           class="w-full flex items-center gap-5 px-6 py-3.5 rounded-[1.25rem] transition-all duration-300 {{ request()->routeIs('transactions.*') ? 'bg-onyx-950 dark:bg-white text-white dark:text-black shadow-2xl scale-105' : 'text-onyx-500 hover:bg-white/5 hover:translate-x-2' }}">
                            <i data-lucide="arrow-left-right" class="w-5 h-5"></i>
                            <span class="text-xs font-black tracking-tight">Transaction Flux</span>
                        </a>
                        <a href="{{ route('scanner') }}" 
                           class="w-full flex items-center gap-5 px-6 py-3.5 rounded-[1.25rem] transition-all duration-300 {{ request()->routeIs('scanner') ? 'bg-onyx-950 dark:bg-white text-white dark:text-black shadow-2xl scale-105' : 'text-onyx-500 hover:bg-white/5 hover:translate-x-2' }}">
                            <i data-lucide="camera" class="w-5 h-5"></i>
                            <span class="text-xs font-black tracking-tight">QR Scanner</span>
                        </a>
                    </div>
                </div>

                @if(Auth::user() && Auth::user()->role === 'admin')
                <!-- Group 2: Intelligence -->
                <div class="space-y-2">
                    <p class="text-[9px] font-black tracking-[0.2em] text-onyx-400 uppercase px-6">Intelligence & Supply</p>
                    <div class="space-y-1">
                        <a href="{{ route('reports.profit') }}" 
                           class="w-full flex items-center gap-5 px-6 py-3.5 rounded-[1.25rem] transition-all duration-300 {{ request()->routeIs('reports.profit') ? 'bg-onyx-950 dark:bg-white text-white dark:text-black shadow-2xl scale-105' : 'text-onyx-500 hover:bg-white/5 hover:translate-x-2' }}">
                            <i data-lucide="pie-chart" class="w-5 h-5"></i>
                            <span class="text-xs font-black tracking-tight">Profit Analytics</span>
                        </a>
                        <a href="{{ route('reports.forecasting') }}" 
                           class="w-full flex items-center gap-5 px-6 py-3.5 rounded-[1.25rem] transition-all duration-300 {{ request()->routeIs('reports.forecasting') ? 'bg-onyx-950 dark:bg-white text-white dark:text-black shadow-2xl scale-105' : 'text-onyx-500 hover:bg-white/5 hover:translate-x-2' }}">
                            <i data-lucide="brain" class="w-5 h-5"></i>
                            <span class="text-xs font-black tracking-tight">AI Predictions</span>
                        </a>
                        <a href="{{ route('reports.expiring') }}" 
                           class="w-full flex items-center gap-5 px-6 py-3.5 rounded-[1.25rem] transition-all duration-300 {{ request()->routeIs('reports.expiring') ? 'bg-onyx-950 dark:bg-white text-white dark:text-black shadow-2xl scale-105' : 'text-onyx-500 hover:bg-white/5 hover:translate-x-2' }}">
                            <i data-lucide="clock" class="w-5 h-5"></i>
                            <span class="text-xs font-black tracking-tight">Expiring Alerts</span>
                        </a>
                        <a href="{{ route('pos.index') }}" 
                           class="w-full flex items-center gap-5 px-6 py-3.5 rounded-[1.25rem] transition-all duration-300 {{ request()->routeIs('pos.*') ? 'bg-onyx-950 dark:bg-white text-white dark:text-black shadow-2xl scale-105' : 'text-onyx-500 hover:bg-white/5 hover:translate-x-2' }}">
                            <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                            <span class="text-xs font-black tracking-tight">Purchase Orders</span>
                        </a>
                    </div>
                </div>

                <!-- Group 3: System Utilities -->
                <div class="space-y-2">
                    <p class="text-[9px] font-black tracking-[0.2em] text-onyx-400 uppercase px-6">System Settings</p>
                    <div class="space-y-1">
                        <a href="{{ route('users.index') }}" 
                           class="w-full flex items-center gap-5 px-6 py-3.5 rounded-[1.25rem] transition-all duration-300 {{ request()->routeIs('users.*') ? 'bg-onyx-950 dark:bg-white text-white dark:text-black shadow-2xl scale-105' : 'text-onyx-500 hover:bg-white/5 hover:translate-x-2' }}">
                            <i data-lucide="users" class="w-5 h-5"></i>
                            <span class="text-xs font-black tracking-tight">User Management</span>
                        </a>
                        <a href="{{ route('backups.index') }}" 
                           class="w-full flex items-center gap-5 px-6 py-3.5 rounded-[1.25rem] transition-all duration-300 {{ request()->routeIs('backups.*') ? 'bg-onyx-950 dark:bg-white text-white dark:text-black shadow-2xl scale-105' : 'text-onyx-500 hover:bg-white/5 hover:translate-x-2' }}">
                            <i data-lucide="database" class="w-5 h-5"></i>
                            <span class="text-xs font-black tracking-tight">System Backups</span>
                        </a>
                    </div>
                </div>
                @endif
            </nav>


            <div class="mt-auto pt-10 border-t border-black/5 dark:border-white/5">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-5 px-6 py-4 rounded-[1.25rem] text-rose-500 hover:bg-rose-500/10 transition-all hover:translate-x-2">
                        <i data-lucide="log-out" class="w-5 h-5"></i>
                        <span class="text-xs font-black uppercase tracking-[0.2em] text-left">Sign Out</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Zenith Bottom Sheet (Full Navigation Hub) -->
        <div x-show="mobileMenuOpen" 
             class="fixed inset-0 z-[2500] bg-black/60 backdrop-blur-md xl:hidden" 
             @click="mobileMenuOpen = false" 
             x-cloak 
             x-transition:opacity></div>
             
        <aside class="fixed left-0 right-0 bottom-0 z-[2501] zen-glass rounded-t-[3rem] max-h-[85vh] overflow-y-auto custom-scrollbar p-10 pt-4 transition-transform duration-500 xl:hidden shadow-[0_-20px_50px_rgba(0,0,0,0.3)] border-t border-white/20"
               :class="mobileMenuOpen ? 'translate-y-0' : 'translate-y-full'" x-cloak>
            
            <div class="flex justify-center sticky top-0 bg-transparent pt-2 pb-6 z-20">
                <div class="w-16 h-1.5 rounded-full bg-onyx-200 dark:bg-white/10"></div>
            </div>

            <div class="space-y-8 pb-20">
                <!-- Group 1: Core Protocols -->
                <div class="space-y-3">
                    <p class="text-[9px] font-black tracking-[0.2em] text-onyx-400 uppercase px-2">Core Protocols</p>
                    <a href="{{ route('dashboard') }}" 
                       class="flex items-center gap-5 p-4 rounded-[1.25rem] {{ request()->routeIs('dashboard') ? 'bg-onyx-950 dark:bg-white text-white dark:text-black shadow-xl font-bold' : 'bg-black/5 dark:bg-white/5 text-onyx-500' }}">
                        <i data-lucide="layout-grid" class="w-5 h-5"></i>
                        <span class="text-xs font-black uppercase tracking-widest">Dashboard</span>
                    </a>
                    <a href="{{ route('items.index') }}" 
                       class="flex items-center gap-5 p-4 rounded-[1.25rem] {{ request()->routeIs('items.*') ? 'bg-onyx-950 dark:bg-white text-white dark:text-black shadow-xl font-bold' : 'bg-black/5 dark:bg-white/5 text-onyx-500' }}">
                        <i data-lucide="package" class="w-5 h-5"></i>
                        <span class="text-xs font-black uppercase tracking-widest">Inventory Registry</span>
                    </a>
                    <a href="{{ route('transactions.index') }}" 
                       class="flex items-center gap-5 p-4 rounded-[1.25rem] {{ request()->routeIs('transactions.*') ? 'bg-onyx-950 dark:bg-white text-white dark:text-black shadow-xl font-bold' : 'bg-black/5 dark:bg-white/5 text-onyx-500' }}">
                        <i data-lucide="arrow-left-right" class="w-5 h-5"></i>
                        <span class="text-xs font-black uppercase tracking-widest">Transaction Flux</span>
                    </a>
                    <a href="{{ route('scanner') }}" 
                       class="flex items-center gap-5 p-4 rounded-[1.25rem] {{ request()->routeIs('scanner') ? 'bg-onyx-950 dark:bg-white text-white dark:text-black shadow-xl font-bold' : 'bg-black/5 dark:bg-white/5 text-onyx-500' }}">
                        <i data-lucide="camera" class="w-5 h-5"></i>
                        <span class="text-xs font-black uppercase tracking-widest">QR Scanner</span>
                    </a>
                </div>

                @if(Auth::user() && Auth::user()->role === 'admin')
                <!-- Group 2: Intelligence & Supply -->
                <div class="space-y-3">
                    <p class="text-[9px] font-black tracking-[0.2em] text-onyx-400 uppercase px-2">Intelligence & Supply</p>
                    <a href="{{ route('reports.profit') }}" 
                       class="flex items-center gap-5 p-4 rounded-[1.25rem] {{ request()->routeIs('reports.profit') ? 'bg-onyx-950 dark:bg-white text-white dark:text-black shadow-xl font-bold' : 'bg-black/5 dark:bg-white/5 text-onyx-500' }}">
                        <i data-lucide="pie-chart" class="w-5 h-5"></i>
                        <span class="text-xs font-black uppercase tracking-widest">Profit Analytics</span>
                    </a>
                    <a href="{{ route('reports.forecasting') }}" 
                       class="flex items-center gap-5 p-4 rounded-[1.25rem] {{ request()->routeIs('reports.forecasting') ? 'bg-onyx-950 dark:bg-white text-white dark:text-black shadow-xl font-bold' : 'bg-black/5 dark:bg-white/5 text-onyx-500' }}">
                        <i data-lucide="brain" class="w-5 h-5"></i>
                        <span class="text-xs font-black uppercase tracking-widest">AI Predictions</span>
                    </a>
                    <a href="{{ route('reports.expiring') }}" 
                       class="flex items-center gap-5 p-4 rounded-[1.25rem] {{ request()->routeIs('reports.expiring') ? 'bg-onyx-950 dark:bg-white text-white dark:text-black shadow-xl font-bold' : 'bg-black/5 dark:bg-white/5 text-onyx-500' }}">
                        <i data-lucide="clock" class="w-5 h-5"></i>
                        <span class="text-xs font-black uppercase tracking-widest">Expiring Alerts</span>
                    </a>
                    <a href="{{ route('pos.index') }}" 
                       class="flex items-center gap-5 p-4 rounded-[1.25rem] {{ request()->routeIs('pos.*') ? 'bg-onyx-950 dark:bg-white text-white dark:text-black shadow-xl font-bold' : 'bg-black/5 dark:bg-white/5 text-onyx-500' }}">
                        <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                        <span class="text-xs font-black uppercase tracking-widest">Purchase Orders</span>
                    </a>
                </div>

                <!-- Group 3: System Utilities -->
                <div class="space-y-3">
                    <p class="text-[9px] font-black tracking-[0.2em] text-onyx-400 uppercase px-2">System Settings</p>
                    <a href="{{ route('users.index') }}" 
                       class="flex items-center gap-5 p-4 rounded-[1.25rem] {{ request()->routeIs('users.*') ? 'bg-onyx-950 dark:bg-white text-white dark:text-black shadow-xl font-bold' : 'bg-black/5 dark:bg-white/5 text-onyx-500' }}">
                        <i data-lucide="users" class="w-5 h-5"></i>
                        <span class="text-xs font-black uppercase tracking-widest">User Management</span>
                    </a>
                    <a href="{{ route('backups.index') }}" 
                       class="flex items-center gap-5 p-4 rounded-[1.25rem] {{ request()->routeIs('backups.*') ? 'bg-onyx-950 dark:bg-white text-white dark:text-black shadow-xl font-bold' : 'bg-black/5 dark:bg-white/5 text-onyx-500' }}">
                        <i data-lucide="database" class="w-5 h-5"></i>
                        <span class="text-xs font-black uppercase tracking-widest">System Backups</span>
                    </a>
                </div>
                @endif

                <form action="{{ route('logout') }}" method="POST" class="pt-6">
                    @csrf
                    <button type="submit" class="w-full py-6 rounded-[2rem] bg-rose-500 text-white font-black uppercase text-xs tracking-[0.3em] shadow-xl shadow-rose-500/20">
                        Terminate Session
                    </button>
                </form>
            </div>
        </aside>

        <!-- Zenith Duo-Dock (Mobile Navigation) -->
        <div class="fixed bottom-10 left-1/2 -translate-x-1/2 z-[2000] flex items-center gap-4 xl:hidden">
            <!-- Sidebar Trigger -->
            <button @click="mobileMenuOpen = true; playFeedback()" class="w-16 h-16 rounded-[1.5rem] zen-glass border border-white/20 shadow-2xl flex items-center justify-center active:scale-90 transition-transform">
                <i data-lucide="menu" class="w-7 h-7 text-onyx-950 dark:text-white"></i>
            </button>
            <!-- AI Whisperer Trigger (Mobile) -->
            <button @click="intelOpen = !intelOpen; playFeedback()" class="w-16 h-16 rounded-[1.5rem] bg-emerald-500 text-white shadow-lg shadow-emerald-500/40 flex items-center justify-center active:scale-90 transition-transform relative">
                <i data-lucide="sparkles" class="w-7 h-7 animate-pulse"></i>
                <span class="absolute top-4 right-4 w-2 h-2 bg-white rounded-full animate-ping"></span>
            </button>
        </div>

        <!-- AI Whisperer FAB (Desktop) -->
        <button @click="intelOpen = !intelOpen; playFeedback()" 
                class="fixed bottom-10 right-10 z-[2000] hidden xl:flex w-20 h-20 rounded-[2rem] bg-emerald-500 text-white shadow-2xl shadow-emerald-500/40 items-center justify-center hover:scale-110 active:scale-95 transition-all group overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-tr from-white/0 via-white/20 to-white/0 -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
            <i data-lucide="sparkles" class="w-8 h-8 group-hover:rotate-12 transition-transform"></i>
            <span class="absolute -top-12 left-1/2 -translate-x-1/2 px-4 py-2 rounded-xl bg-onyx-950 text-[10px] font-black uppercase tracking-widest opacity-0 group-hover:opacity-100 transition-all shadow-2xl whitespace-nowrap">AI Whisperer</span>
        </button>

        <!-- Main Workspace -->
        <main class="flex-1 flex flex-col gap-6 min-w-0 overflow-y-auto custom-scrollbar pb-32 xl:pb-0">
            <div class="max-w-[1700px] w-full mx-auto flex flex-col gap-6">
                <header class="flex items-center justify-between gap-6 px-2 stagger" style="animation-delay: 0.1s">
                    <div class="flex flex-col">
                        <div class="flex items-center gap-3 mb-2">
                            <nav class="flex items-center gap-3 text-[10px] font-black uppercase tracking-[0.4em] text-onyx-400">
                                <a href="{{ route('dashboard') }}" class="hover:text-onyx-950 dark:hover:text-white transition-colors">HOME</a>
                                @hasSection('breadcrumb')
                                    <i data-lucide="chevron-right" class="w-3 h-3 text-onyx-300"></i>
                                    @yield('breadcrumb')
                                @endif
                            </nav>
                        </div>
                        <h2 class="text-3xl md:text-6xl font-black tracking-tighter italic leading-none uppercase">
                            @yield('page_title', 'Dashboard')
                        </h2>
                    </div>

                    <div class="flex items-center gap-4">
                        <button @click="toggleDarkMode()" class="w-14 h-14 rounded-2xl zen-glass flex items-center justify-center magnetic-btn">
                            <i data-lucide="sun" x-show="darkMode" class="w-6 h-6 glow-icon"></i>
                            <i data-lucide="moon" x-show="!darkMode" class="w-6 h-6 text-onyx-950 glow-icon"></i>
                        </button>
                        <div class="hidden md:flex items-center gap-4 p-2 pl-4 rounded-2xl zen-glass">
                            <div class="text-right">
                                <p class="text-[10px] font-black truncate uppercase tracking-widest">{{ Auth::user()->name }}</p>
                                <p class="text-[8px] font-black text-onyx-400 uppercase tracking-widest">{{ Auth::user()->role }}</p>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-onyx-950 dark:bg-white text-white dark:text-black flex items-center justify-center font-black text-xs">
                                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <div class="w-full animate-in fade-in zoom-in duration-700">
                    @yield('content')
                </div>
            </div>
        </main>
    </div>



    <!-- Zenith Command Palette -->
    <div x-show="commandPaletteOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-[2000] flex items-start justify-center pt-32 px-6" x-cloak>
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="commandPaletteOpen = false"></div>
        <div class="w-full max-w-2xl zen-glass rounded-[2.5rem] shadow-2xl overflow-hidden relative z-[2001]">
            <div class="p-8 border-b border-black/5 dark:border-white/10 flex items-center gap-6">
                <i data-lucide="command" class="w-6 h-6 text-onyx-400"></i>
                <input x-ref="commandInput" x-model="commandQuery" type="text" 
                       class="w-full bg-transparent border-none text-2xl font-black italic uppercase tracking-tighter placeholder:text-onyx-300 dark:placeholder:text-onyx-700 focus:ring-0 outline-none" 
                       placeholder="EXECUTE PROTOCOL...">
                <kbd class="hidden md:block px-3 py-1 rounded-lg bg-black/5 dark:bg-white/5 text-[10px] font-black uppercase tracking-widest text-onyx-400">ESC</kbd>
            </div>
            <div class="max-h-[400px] overflow-y-auto custom-scrollbar p-6 space-y-2">
                <template x-for="cmd in filteredCommands">
                    <a :href="cmd.route" class="flex items-center justify-between p-5 rounded-2xl hover:bg-black/5 dark:hover:bg-white/5 transition-all group">
                        <div class="flex items-center gap-6">
                            <div class="w-10 h-10 rounded-xl bg-black/5 dark:bg-white/5 flex items-center justify-center group-hover:bg-emerald-500 group-hover:text-white transition-colors">
                                <i :data-lucide="cmd.icon" class="w-5 h-5"></i>
                            </div>
                            <span class="text-sm font-black uppercase tracking-widest" x-text="cmd.name"></span>
                        </div>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-onyx-300 opacity-0 group-hover:opacity-100 transition-all"></i>
                    </a>
                </template>
                <div x-show="filteredCommands.length === 0" class="p-10 text-center text-onyx-400">
                    <p class="text-xs font-black uppercase tracking-widest">No matching protocols found.</p>
                </div>
            </div>
            <div class="p-6 bg-black/5 dark:bg-white/5 border-t border-black/5 dark:border-white/10 flex items-center justify-between">
                <p class="text-[9px] font-black text-onyx-400 uppercase tracking-widest italic">StockMaster Hub V12.4 • Zenith Protocol</p>
            </div>
        </div>
    </div>

    <!-- AI Inventory Whisperer (Sliding Sidebar) -->
    <div x-show="intelOpen" class="fixed inset-0 z-[1500] bg-black/40 backdrop-blur-sm" @click="intelOpen = false" x-cloak x-transition:opacity></div>
    <aside class="fixed right-0 top-0 bottom-0 w-full md:w-[500px] z-[1501] zen-glass border-l border-white/10 flex flex-col transition-transform duration-700"
           :class="intelOpen ? 'translate-x-0' : 'translate-x-full'" x-cloak>
        <div class="p-10 border-b border-black/5 dark:border-white/10 flex items-center justify-between bg-white/50 dark:bg-black/50">
            <div class="flex items-center gap-5">
                <div class="w-12 h-12 bg-emerald-500 rounded-2xl flex items-center justify-center shadow-xl shadow-emerald-500/20">
                    <i data-lucide="sparkles" class="w-6 h-6 text-white animate-pulse"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-black italic tracking-tighter uppercase">Synaptic AI</h3>
                    <div class="flex items-center gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                        <p class="text-[9px] font-black uppercase tracking-[0.3em] text-emerald-500">Neural Link Active</p>
                    </div>
                </div>
            </div>
            <button @click="intelOpen = false" class="w-10 h-10 rounded-xl hover:bg-black/5 dark:hover:bg-white/5 flex items-center justify-center text-onyx-400">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-10 space-y-8 chat-scrollbar" x-ref="chatContainer">
            <template x-for="msg in aiMessages">
                <div class="flex flex-col" :class="msg.role === 'user' ? 'items-end' : 'items-start'">
                    <div class="max-w-[85%] p-6 rounded-[2rem] shadow-sm" 
                         :class="msg.role === 'user' ? 'bg-onyx-950 dark:bg-white text-white dark:text-black rounded-tr-none' : 'zen-glass rounded-tl-none border-emerald-500/20'">
                        <p class="text-sm leading-relaxed" x-text="msg.text"></p>
                    </div>
                    <span class="text-[8px] font-black uppercase tracking-widest text-onyx-400 mt-2 px-2" x-text="msg.time"></span>
                </div>
            </template>
            <div x-show="aiLoading" class="space-y-4 animate-in fade-in duration-500">
                <div class="flex flex-col gap-4">
                    <div class="w-48 h-4 rounded-full zenith-shimmer bg-black/5 dark:bg-white/5"></div>
                    <div class="w-full h-20 rounded-[2rem] zenith-shimmer bg-black/5 dark:bg-white/5"></div>
                    <div class="w-32 h-4 rounded-full zenith-shimmer bg-black/5 dark:bg-white/5"></div>
                </div>
                <div class="flex items-center gap-2 px-2">
                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                    <span class="text-[8px] font-black text-emerald-500 uppercase tracking-widest italic">Synchronizing Neural Path...</span>
                </div>
            </div>
        </div>

        <div class="p-8 border-t border-black/5 dark:border-white/10 bg-black/5 dark:bg-white/5">
            <div class="relative group">
                <textarea x-model="aiQuery" @keydown.enter.prevent="sendAiMessage()"
                          class="w-full bg-white dark:bg-onyx-900 border border-black/10 dark:border-white/10 rounded-[2rem] p-6 pr-20 text-sm focus:ring-2 focus:ring-emerald-500/50 transition-all outline-none resize-none shadow-inner"
                          placeholder="Ask the engine..."></textarea>
                <button @click="sendAiMessage()" class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-emerald-500 text-white rounded-full flex items-center justify-center hover:scale-110 active:scale-95 transition-transform shadow-lg shadow-emerald-500/30">
                    <i data-lucide="send" class="w-5 h-5"></i>
                </button>
            </div>
            <p class="text-[8px] font-black text-onyx-300 dark:text-onyx-600 uppercase tracking-widest mt-4 text-center italic">Neural Link v2.0 • Real-time Intelligence</p>
        </div>
    </aside>

    <script>
        lucide.createIcons();
        
        let audioContext = null;
        window.playFeedback = function(type = 'click') {
            try {
                if (!audioContext) audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const osc = audioContext.createOscillator();
                const gain = audioContext.createGain();
                osc.connect(gain);
                gain.connect(audioContext.destination);
                
                if (type === 'click') {
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(800, audioContext.currentTime);
                    osc.frequency.exponentialRampToValueAtTime(1200, audioContext.currentTime + 0.05);
                    gain.gain.setValueAtTime(0.08, audioContext.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.001, audioContext.currentTime + 0.1);
                    osc.start(); osc.stop(audioContext.currentTime + 0.1);
                } else if (type === 'success') {
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(800, audioContext.currentTime);
                    osc.frequency.exponentialRampToValueAtTime(1600, audioContext.currentTime + 0.2);
                    gain.gain.setValueAtTime(0.1, audioContext.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.001, audioContext.currentTime + 0.3);
                    osc.start(); osc.stop(audioContext.currentTime + 0.3);
                } else if (type === 'hover') {
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(400, audioContext.currentTime);
                    osc.frequency.exponentialRampToValueAtTime(600, audioContext.currentTime + 0.02);
                    gain.gain.setValueAtTime(0.02, audioContext.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.001, audioContext.currentTime + 0.03);
                    osc.start(); osc.stop(audioContext.currentTime + 0.03);
                }
            } catch (e) {
                // browser blocks audio until interaction
            }
        };

        function refreshCharts() {
            if(typeof initChart === 'function') {
                const isDark = document.documentElement.classList.contains('dark');
                initChart(isDark);
            }
        }
        
        document.addEventListener('DOMContentLoaded', refreshCharts);
        window.addEventListener('resize', () => {
            if(window.chart) window.chart.resize();
        });
    </script>
    @stack('scripts')
</body>
</html>