@extends('layouts')

@section('page_title', 'Scanner')

@section('breadcrumb')
    <span class="text-onyx-400">PROTOCOL</span>
    <i data-lucide="chevron-right" class="w-3 h-3 text-onyx-300"></i>
    <span class="text-onyx-950 dark:text-white">SCANNER</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-10 animate-in fade-in slide-in-from-bottom-4 duration-700">
    <!-- Header Section -->
    <div class="text-center space-y-4">
        <h1 class="text-5xl lg:text-7xl font-black tracking-tighter italic leading-none uppercase">Scanner</h1>
        <p class="text-[10px] font-black uppercase tracking-[0.3em] text-onyx-400 mt-2">Optical Inventory Identification Protocol</p>
    </div>

    <!-- Scanner Interface -->
    <div class="zen-glass squircle p-10 relative overflow-hidden">
        <div class="absolute -top-24 -right-24 w-64 h-64 bg-indigo-500/5 rounded-full blur-3xl"></div>
        
        <div class="relative z-10 space-y-10">
            <!-- Camera Viewport -->
            <div id="reader" class="rounded-[2rem] overflow-hidden border-8 border-black/5 dark:border-white/5 shadow-inner bg-black/20"></div>
            
            <!-- Result Display -->
            <div id="result" class="hidden animate-in zoom-in duration-500">
                <div class="p-10 zen-glass rounded-[2rem] border-emerald-500/20 bg-emerald-500/5">
                    <div class="flex flex-col md:flex-row items-center gap-8">
                        <div class="w-20 h-20 bg-emerald-500 rounded-full flex items-center justify-center text-white shadow-[0_20px_50px_rgba(16,185,129,0.4)]">
                            <i data-lucide="check-circle" class="w-10 h-10"></i>
                        </div>
                        <div class="flex-1 text-center md:text-left">
                            <p class="text-[10px] font-black text-emerald-500 uppercase tracking-[0.4em] mb-2 italic">Asset Verified</p>
                            <p id="scanned-result" class="text-2xl font-black text-onyx-950 dark:text-white truncate italic tracking-tighter"></p>
                        </div>
                        <a id="redirect-link" href="#" class="px-10 py-5 bg-onyx-950 dark:bg-white text-white dark:text-black rounded-2xl font-black text-[10px] uppercase tracking-widest italic hover:scale-105 transition-all shadow-2xl">
                            Explore Asset
                        </a>
                    </div>
                </div>
            </div>

            <!-- Controls -->
            <div class="flex flex-col md:flex-row gap-6">
                <button onclick="startScanner()" class="flex-1 py-6 bg-onyx-950 dark:bg-white text-white dark:text-black rounded-2xl font-black text-xs uppercase tracking-[0.4em] italic flex items-center justify-center gap-4 hover:scale-[1.02] active:scale-95 transition-all shadow-2xl">
                    <i data-lucide="camera" class="w-6 h-6"></i>
                    Initialize Camera
                </button>
                <button onclick="stopScanner()" class="px-10 py-6 zen-glass rounded-2xl font-black text-xs uppercase tracking-[0.4em] italic text-onyx-400 hover:text-rose-500 transition-all flex items-center justify-center gap-4">
                    <i data-lucide="stop-circle" class="w-6 h-6"></i>
                    Deactivate
                </button>
            </div>
        </div>
    </div>

    <!-- Protocol Info -->
    <div class="zen-glass rounded-[2rem] p-8 bg-amber-500/5 border-amber-500/10">
        <div class="flex items-center gap-6">
            <div class="w-12 h-12 rounded-xl bg-amber-500/20 flex items-center justify-center text-amber-500">
                <i data-lucide="info" class="w-6 h-6"></i>
            </div>
            <p class="text-[10px] font-black uppercase tracking-widest text-amber-600/80 leading-relaxed">
                Ensure optimal illumination and center the QR identifier within the viewport frame for high-fidelity detection.
            </p>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    let html5QrCode;

    function startScanner() {
        html5QrCode = new Html5Qrcode("reader");
        const qrCodeSuccessCallback = (decodedText, decodedResult) => {
            document.getElementById('result').classList.remove('hidden');
            document.getElementById('scanned-result').innerText = decodedText;
            document.getElementById('redirect-link').href = decodedText;
            
            if (decodedText.includes(window.location.origin)) {
                setTimeout(() => {
                    window.location.href = decodedText;
                }, 1200);
            }
            
            lucide.createIcons();
            stopScanner();
        };
        const config = { fps: 15, qrbox: { width: 280, height: 280 } };

        html5QrCode.start({ facingMode: "environment" }, config, qrCodeSuccessCallback)
            .catch((err) => {
                alert("Camera protocol initialization failed.");
            });
    }

    function stopScanner() {
        if (html5QrCode) {
            html5QrCode.stop().then((ignore) => {
                console.log("Scanner protocol offline.");
            });
        }
    }
</script>
@endpush
@endsection
