@extends('layouts')

@section('page_title', 'Encyclopedia of Intelligence')

@section('breadcrumb')
    <i data-lucide="chevron-right" class="w-3 h-3 text-onyx-300"></i>
    <span class="text-onyx-950 dark:text-white uppercase tracking-widest text-[10px] font-black">ENCYCLOPEDIA</span>
@endsection

@section('content')
<div class="space-y-24 pb-32">
    <!-- Hero Header -->
    <div class="zen-glass p-20 rounded-[5rem] relative overflow-hidden stagger" style="animation-delay: 0.1s">
        <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-emerald-500/5 to-transparent"></div>
        <div class="relative z-10 max-w-3xl">
            <div class="inline-flex items-center gap-3 px-5 py-2.5 rounded-full bg-onyx-950 dark:bg-white text-white dark:text-black mb-10">
                <i data-lucide="book-open" class="w-4 h-4"></i>
                <span class="text-[10px] font-black uppercase tracking-[0.4em]">Official Protocol v12.4</span>
            </div>
            <h3 class="text-6xl lg:text-8xl font-black italic uppercase tracking-tighter text-onyx-950 dark:text-white leading-[0.8] mb-10">
                Master The<br/>Enterprise Hub
            </h3>
            <p class="text-xl text-onyx-500 dark:text-onyx-400 font-medium leading-relaxed">
                Panduan komprehensif untuk menguasai setiap modul dalam ekosistem StockMaster. Dari manajemen aset dasar hingga analisis prediktif berbasis AI.
            </p>
        </div>
    </div>

    <!-- Section 1: Central Intelligence (Dashboard) -->
    <section class="space-y-12 stagger" style="animation-delay: 0.2s">
        <div class="flex items-center gap-6 px-4">
            <div class="h-1.5 w-12 bg-emerald-500 rounded-full"></div>
            <h2 class="text-3xl font-black italic uppercase tracking-tighter">01. Central Intelligence</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="zen-glass p-12 rounded-[4rem] tilt-card">
                <i data-lucide="activity" class="w-12 h-12 text-emerald-500 mb-8"></i>
                <h4 class="text-2xl font-black italic uppercase tracking-tighter mb-4">Intelligence Pulse</h4>
                <p class="text-sm text-onyx-500 dark:text-onyx-400 leading-relaxed mb-6">Grafik real-time yang melacak aliran transaksi (Inbound/Outbound) selama 7 hari terakhir. Digunakan untuk mendeteksi lonjakan aktivitas atau penurunan stok secara visual.</p>
                <span class="text-[9px] font-black uppercase tracking-widest text-emerald-600 bg-emerald-500/10 px-3 py-1 rounded-full">AI Monitoring Active</span>
            </div>
            <div class="zen-glass p-12 rounded-[4rem] tilt-card">
                <i data-lucide="pie-chart" class="w-12 h-12 text-blue-500 mb-8"></i>
                <h4 class="text-2xl font-black italic uppercase tracking-tighter mb-4">Asset Allocation</h4>
                <p class="text-sm text-onyx-500 dark:text-onyx-400 leading-relaxed mb-6">Visualisasi distribusi aset berdasarkan kategori. Membantu Anda memahami sektor mana yang paling mendominasi nilai valuasi gudang Anda.</p>
                <span class="text-[9px] font-black uppercase tracking-widest text-blue-600 bg-blue-500/10 px-3 py-1 rounded-full">Valuation Tracking</span>
            </div>
        </div>
    </section>

    <!-- Section 2: Core Inventory Architecture -->
    <section class="space-y-12 stagger" style="animation-delay: 0.3s">
        <div class="flex items-center gap-6 px-4">
            <div class="h-1.5 w-12 bg-amber-500 rounded-full"></div>
            <h2 class="text-3xl font-black italic uppercase tracking-tighter">02. Core Inventory</h2>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="zen-glass p-10 rounded-[3.5rem] tilt-card">
                <i data-lucide="package" class="w-10 h-10 text-amber-500 mb-8"></i>
                <h4 class="text-xl font-black italic uppercase tracking-tighter mb-4">Stock Items</h4>
                <p class="text-xs text-onyx-500 dark:text-onyx-400 leading-relaxed">Pusat kontrol setiap unit barang. Di sini Anda dapat mengatur harga, mencetak label barcode, dan memantau level stok minimum secara otomatis.</p>
            </div>
            <div class="zen-glass p-10 rounded-[3.5rem] tilt-card">
                <i data-lucide="truck" class="w-10 h-10 text-rose-500 mb-8"></i>
                <h4 class="text-xl font-black italic uppercase tracking-tighter mb-4">Vendor Hub</h4>
                <p class="text-xs text-onyx-500 dark:text-onyx-400 leading-relaxed">Manajemen supplier terintegrasi. Lacak performa pengiriman, harga kontrak, dan riwayat pesanan dari setiap mitra bisnis.</p>
            </div>
            <div class="zen-glass p-10 rounded-[3.5rem] tilt-card">
                <i data-lucide="tag" class="w-10 h-10 text-emerald-500 mb-8"></i>
                <h4 class="text-xl font-black italic uppercase tracking-tighter mb-4">Neural Taxonomy</h4>
                <p class="text-xs text-onyx-500 dark:text-onyx-400 leading-relaxed">Gunakan Kategori dan Unit System untuk merapikan struktur data. Memudahkan pencarian dan pengelompokan laporan keuangan.</p>
            </div>
        </div>
    </section>

    <!-- Section 3: Operational Workflow -->
    <section class="space-y-12 stagger" style="animation-delay: 0.4s">
        <div class="flex items-center gap-6 px-4">
            <div class="h-1.5 w-12 bg-indigo-500 rounded-full"></div>
            <h2 class="text-3xl font-black italic uppercase tracking-tighter">03. Operational Dynamics</h2>
        </div>
        <div class="zen-glass p-12 rounded-[4.5rem] tilt-card">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-16">
                <div class="space-y-6">
                    <div class="w-14 h-14 rounded-2xl bg-indigo-500/10 flex items-center justify-center">
                        <i data-lucide="arrow-left-right" class="w-7 h-7 text-indigo-500"></i>
                    </div>
                    <h4 class="text-2xl font-black italic uppercase tracking-tighter">Transactions</h4>
                    <p class="text-xs text-onyx-500 dark:text-onyx-400 leading-relaxed">Setiap mutasi barang dicatat dengan stempel waktu milidetik. Gunakan 'Inbound' untuk stok masuk dan 'Outbound' untuk pengeluaran.</p>
                </div>
                <div class="space-y-6">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 flex items-center justify-center">
                        <i data-lucide="send" class="w-7 h-7 text-emerald-500"></i>
                    </div>
                    <h4 class="text-2xl font-black italic uppercase tracking-tighter">Transfers</h4>
                    <p class="text-xs text-onyx-500 dark:text-onyx-400 leading-relaxed">Pindahkan aset antar cabang atau rak penyimpanan tanpa merusak saldo total. Transparansi penuh dalam perpindahan fisik barang.</p>
                </div>
                <div class="space-y-6">
                    <div class="w-14 h-14 rounded-2xl bg-amber-500/10 flex items-center justify-center">
                        <i data-lucide="shopping-bag" class="w-7 h-7 text-amber-500"></i>
                    </div>
                    <h4 class="text-2xl font-black italic uppercase tracking-tighter">Smart Orders</h4>
                    <p class="text-xs text-onyx-500 dark:text-onyx-400 leading-relaxed">Sistem POS untuk pembuatan Purchase Order otomatis saat stok mendekati batas kritis (Critical Threshold).</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 4: Neural Analytics (Reports) -->
    <section class="space-y-12 stagger" style="animation-delay: 0.5s">
        <div class="flex items-center gap-6 px-4">
            <div class="h-1.5 w-12 bg-rose-500 rounded-full"></div>
            <h2 class="text-3xl font-black italic uppercase tracking-tighter">04. Neural Analytics</h2>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <div class="lg:col-span-8 zen-glass p-12 rounded-[4rem] tilt-card flex flex-col md:flex-row gap-12 items-center">
                <div class="flex-1 space-y-6">
                    <h4 class="text-3xl font-black italic uppercase tracking-tighter text-rose-500">Stock Forecasting</h4>
                    <p class="text-sm text-onyx-500 dark:text-onyx-400 leading-relaxed">Mesin AI StockMaster memprediksi kapan barang Anda akan habis berdasarkan tren penjualan sebelumnya. Jangan pernah kehabisan stok lagi dengan algoritma deteksi dini.</p>
                    <div class="flex gap-4">
                        <div class="px-6 py-3 rounded-2xl bg-onyx-950 dark:bg-white text-white dark:text-black text-[10px] font-black uppercase tracking-widest">Run Analysis</div>
                    </div>
                </div>
                <div class="w-48 h-48 rounded-full border-[12px] border-rose-500/10 flex items-center justify-center">
                    <i data-lucide="brain-circuit" class="w-16 h-16 text-rose-500 animate-pulse"></i>
                </div>
            </div>
            <div class="lg:col-span-4 zen-glass p-10 rounded-[4rem] tilt-card space-y-8">
                <i data-lucide="map" class="w-12 h-12 text-indigo-500"></i>
                <h4 class="text-xl font-black italic uppercase tracking-tighter">Activity Heatmap</h4>
                <p class="text-xs text-onyx-500 dark:text-onyx-400 leading-relaxed">Visualisasi jam sibuk gudang Anda. Optimalkan jumlah staf pada jam-jam puncak aktivitas transaksi.</p>
            </div>
        </div>
    </section>

    <!-- Section 5: Enterprise Ecosystem -->
    <section class="space-y-12 stagger" style="animation-delay: 0.6s">
        <div class="flex items-center gap-6 px-4">
            <div class="h-1.5 w-12 bg-sky-500 rounded-full"></div>
            <h2 class="text-3xl font-black italic uppercase tracking-tighter">05. Enterprise Ecosystem</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach([
                ['users-2', 'Customer Engine', 'Manajemen klien dan riwayat belanja pelanggan.', 'sky'],
                ['check-square', 'Reconciliation', 'Audit stok fisik vs sistem secara periodik.', 'emerald'],
                ['history', 'Activity Logs', 'Rekaman setiap klik dan perubahan sistem.', 'onyx'],
                ['user-plus', 'User Control', 'Atur hak akses admin dan staf gudang.', 'indigo']
            ] as $ent)
            <div class="zen-glass p-10 rounded-[3rem] tilt-card text-center group">
                <div class="w-14 h-14 rounded-2xl bg-{{ $ent[3] }}-500/10 flex items-center justify-center mx-auto mb-8 group-hover:scale-110 transition-transform">
                    <i data-lucide="{{ $ent[0] }}" class="w-7 h-7 text-{{ $ent[3] }}-500"></i>
                </div>
                <h4 class="text-sm font-black italic uppercase tracking-tighter mb-3">{{ $ent[1] }}</h4>
                <p class="text-[10px] text-onyx-500 dark:text-onyx-400 leading-relaxed">{{ $ent[2] }}</p>
            </div>
            @endforeach
        </div>
    </section>

    <!-- Final: Security Protocols -->
    <section class="stagger" style="animation-delay: 0.7s">
        <div class="zen-glass p-20 rounded-[5rem] text-center relative overflow-hidden border-rose-500/20">
            <div class="absolute inset-0 bg-gradient-to-b from-rose-500/5 to-transparent"></div>
            <i data-lucide="shield-check" class="w-20 h-20 text-rose-500 mx-auto mb-10"></i>
            <h2 class="text-5xl font-black italic uppercase tracking-tighter mb-8">Data Integrity & Security</h2>
            <p class="text-onyx-500 dark:text-onyx-400 max-w-2xl mx-auto leading-relaxed mb-12">
                StockMaster menggunakan enkripsi Tier-1 dan sistem Auto-Backup untuk memastikan data inventaris Anda aman dari gangguan eksternal dan kehilangan sistem.
            </p>
            <div class="flex justify-center gap-8">
                <div class="flex items-center gap-4 text-[10px] font-black uppercase tracking-widest text-emerald-500">
                    <i data-lucide="check-circle" class="w-5 h-5"></i> End-to-End Encrypted
                </div>
                <div class="flex items-center gap-4 text-[10px] font-black uppercase tracking-widest text-blue-500">
                    <i data-lucide="check-circle" class="w-5 h-5"></i> 24/7 Auto Backup
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
