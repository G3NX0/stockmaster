@extends('layouts')

@section('page_title', 'Overview')

@section('breadcrumb')
    <span class="text-onyx-950 dark:text-white">DASHBOARD</span>
@endsection

@push('scripts')
<script>
function dashboardData() {
    return {
        selectedDate:  null,
        drillDownData: null,
        showDrillDown: false,
        expandedStat:  null,
        totalItems:    {{ $totalItems }},
        totalStock:    {{ $totalStock }},
        totalValue:    {{ $totalValue }},
        lowStockCount: {{ $lowStockCount }},
        transactions:  {!! json_encode($recentTransactionsFeed) !!},
        latestId:      {{ $latestTransactionId }},
        pollInterval:  null,

        openDrillDown(date, inbound, outbound) {
            this.selectedDate  = date;
            this.drillDownData = { in: inbound, out: outbound };
            this.showDrillDown = true;
            playFeedback('success');
        },

        async poll() {
            try {
                const res  = await fetch(`/dashboard/poll?last_id=${this.latestId}`);
                const data = await res.json();
                if (data.transactions && data.transactions.length > 0) {
                    data.transactions.forEach(tx => {
                        if (!this.transactions.find(t => t.id === tx.id)) {
                            this.transactions.unshift(tx);
                            if (this.transactions.length > 6) this.transactions.pop();
                        }
                    });
                    this.latestId      = data.latest_id;
                    this.totalItems    = data.stats.totalItems;
                    this.totalStock    = data.stats.totalStock;
                    this.totalValue    = data.stats.totalValue;
                    this.lowStockCount = data.stats.lowStockCount;
                    playFeedback('success');
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: { message: 'Transaksi baru masuk!', type: 'success' }
                    }));
                }
            } catch(e) { /* silent fail */ }
        },

        init() {
            this.pollInterval = setInterval(() => this.poll(), 5000);
        }
    };
}
</script>
@endpush

@section('content')
<div x-data="dashboardData()"
     class="animate-in fade-in slide-in-from-bottom-4 duration-700 pb-20"
     x-init="init()"
     @destroy.window="clearInterval(pollInterval)">

    <!-- Widescreen Responsive Layout Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left Column: Control Widgets (3 Cols) -->
        <div class="lg:col-span-3 flex flex-col gap-5">
            
            <!-- Live Transaction Feed Widget -->
            <div class="zen-glass squircle p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-[10px] font-black uppercase tracking-wider text-onyx-400">Live Transaction Feed</h3>
                    <div class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </div>
                </div>
                <div class="space-y-2.5 max-h-[220px] overflow-y-auto custom-scrollbar pr-1">
                    <template x-for="tx in transactions" :key="tx.id">
                        <div class="flex items-center gap-3 p-2.5 rounded-xl bg-black/5 dark:bg-white/5 border border-white/5 hover:border-emerald-500/10 transition-all">
                            <div class="w-8 h-8 rounded-lg bg-onyx-900 dark:bg-white text-white dark:text-black flex items-center justify-center shrink-0">
                                <!-- Dynamic Inline SVG Arrows to avoid Lucide lazy load/empty block issue -->
                                <template x-if="tx.type === 'in'">
                                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 4.5l-15 15m0 0h11.25m-11.25 0V8.25"/>
                                    </svg>
                                </template>
                                <template x-if="tx.type === 'out'">
                                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25"/>
                                    </svg>
                                </template>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[10px] font-black uppercase truncate" x-text="tx.item_name"></p>
                                <p class="text-[8px] font-bold text-onyx-400 uppercase tracking-widest mt-0.5" x-text="tx.time"></p>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] font-black italic" :class="tx.type === 'in' ? 'text-emerald-500' : 'text-rose-500'" x-text="(tx.type === 'in' ? '+' : '-') + tx.quantity"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Global Stock Heatmap Widget -->
            <div class="zen-glass squircle p-6 space-y-4">
                <h3 class="text-[10px] font-black uppercase tracking-wider text-onyx-400">Global Stock Heatmap</h3>
                <div class="grid grid-cols-8 gap-2">
                    @foreach($heatmapData as $hm)
                        @php
                            $levels = ['bg-emerald-500/5', 'bg-emerald-500/20', 'bg-emerald-500/40', 'bg-emerald-500/70', 'bg-emerald-500/90'];
                            $levelClass = $levels[$hm['level'] ?? 0];
                        @endphp
                        <div class="aspect-square rounded-md {{ $levelClass }} hover:scale-110 hover:shadow-lg hover:shadow-emerald-500/20 transition-all cursor-pointer" title="{{ $hm['name'] }} (Stok: {{ $hm['stock'] }})"></div>
                    @endforeach
                </div>
            </div>

            <!-- Forecasted Sales Widget -->
            <div class="zen-glass squircle p-6 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-[10px] font-black uppercase tracking-wider text-onyx-400">Forecasted Sales</h3>
                    <span class="text-[8px] font-black text-emerald-500 uppercase tracking-widest italic">micro</span>
                </div>
                <div class="h-12 w-full relative">
                    <canvas id="forecastSparkline"></canvas>
                </div>
            </div>

            <!-- AI-Driven Predictions Widget -->
            <div class="zen-glass squircle p-6 space-y-4">
                <h3 class="text-[10px] font-black uppercase tracking-wider text-onyx-400">AI-Driven Predictions</h3>
                <div class="space-y-2.5 text-[9px]">
                    @foreach($aiPredictions as $pred)
                        <div class="p-3 rounded-xl bg-{{ $pred['color'] }}-500/5 border border-{{ $pred['color'] }}-500/10 flex justify-between items-center gap-3">
                            <p class="font-semibold text-onyx-400 leading-tight flex-1">{{ $pred['text'] }}</p>
                            <span class="font-black text-{{ $pred['color'] }}-500 whitespace-nowrap">{{ $pred['value'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

        <!-- Right Column: Workspace & Bento (9 Cols) -->
        <div class="lg:col-span-9 flex flex-col gap-6">
            
            @if(auth()->user()->role === 'admin')
            <!-- Action Row (Aligned nicely with overview) -->
            <div class="flex justify-end items-center gap-4 px-2" x-data="{ sendingReport: false, reportType: 'daily' }">
                <!-- Select Report Type (Custom Premium Dropdown) -->
                <div class="relative" x-data="{ open: false, labelMap: { daily: 'Laporan Harian', weekly: 'Laporan Mingguan', monthly: 'Laporan Bulanan', 'stock-critical': 'Stok Kritis', summary: 'Ringkasan Sistem' } }" @click.away="open = false">
                    <button @click="open = !open; playFeedback('click')"
                            type="button"
                            class="flex items-center justify-between gap-6 pl-5 pr-12 py-3 rounded-[1.25rem] border border-black/10 dark:border-white/15 bg-black/80 dark:bg-onyx-950/80 text-white text-xs font-black uppercase tracking-widest transition-all cursor-pointer shadow-lg relative min-w-[210px] text-left hover:scale-[1.02] active:scale-[0.98]">
                        <span x-text="labelMap[reportType]"></span>
                        <div class="absolute inset-y-0 right-0 flex items-center px-4 text-onyx-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-4 h-4 transition-transform duration-300" :class="open ? 'rotate-180' : ''">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>
                    </button>
                    
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                         class="absolute right-0 mt-3 w-60 rounded-[1.25rem] bg-white/95 dark:bg-onyx-950/95 border border-black/10 dark:border-white/15 shadow-2xl p-2 space-y-1 z-[200] max-h-64 overflow-y-auto custom-scrollbar"
                         style="display: none;">
                        
                        <template x-for="(label, val) in labelMap">
                            <button type="button"
                                    @click="reportType = val; open = false; playFeedback('click')"
                                    class="w-full text-left px-4 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all flex items-center justify-between"
                                    :class="reportType === val ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/20' : 'text-stone-700 dark:text-onyx-400 hover:bg-black/5 dark:hover:bg-white/5 hover:text-black dark:hover:text-white'">
                                <span x-text="label"></span>
                                <svg x-show="reportType === val" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-3.5 h-3.5 text-white">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Action Button -->
                <button @click="
                            sendingReport = true; 
                            playFeedback('click'); 
                            fetch('{{ route('dashboard.send-whatsapp-report') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ report_type: reportType })
                            })
                            .then(res => res.json())
                            .then(data => {
                                sendingReport = false;
                                if (data.success) {
                                    window.dispatchEvent(new CustomEvent('show-toast', {
                                        detail: { message: data.message, type: 'success' }
                                    }));
                                } else {
                                    window.dispatchEvent(new CustomEvent('show-toast', {
                                        detail: { message: data.message, type: 'error' }
                                    }));
                                }
                            })
                            .catch(err => {
                                sendingReport = false;
                                window.dispatchEvent(new CustomEvent('show-toast', {
                                    detail: { message: 'Koneksi gagal', type: 'error' }
                                }));
                            })
                        "
                        :disabled="sendingReport"
                        @mouseenter="playFeedback('hover')"
                        class="px-6 py-3 rounded-[1.25rem] bg-emerald-500 hover:bg-emerald-400 text-white flex items-center gap-3 text-xs font-black uppercase tracking-widest disabled:opacity-50 transition-all hover:shadow-[0_0_20px_rgba(16,185,129,0.3)] border-none cursor-pointer">
                    <template x-if="!sendingReport">
                        <div class="flex items-center gap-3">
                            <i data-lucide="share-2" class="w-4 h-4"></i>
                            <span x-text="'Kirim ' + (reportType === 'daily' ? 'Lap. Harian' : reportType === 'weekly' ? 'Lap. Mingguan' : reportType === 'monthly' ? 'Lap. Bulanan' : reportType === 'stock-critical' ? 'Stok Kritis' : 'Ringkasan') + ' ke WA'"></span>
                        </div>
                    </template>
                    <template x-if="sendingReport">
                        <div class="flex items-center gap-3">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Mengirim...</span>
                        </div>
                    </template>
                </button>
            </div>
            @endif

            <!-- Main Bento Layout (4 Cards + 1 Sidebar Widget) -->
            <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
                
                <!-- Inner Metrics (col-span-8) -->
                <div class="xl:col-span-9 grid grid-cols-1 md:grid-cols-4 gap-4">
                    
                    <!-- Total Assets -->
                    <div class="zen-glass squircle p-5 flex items-center gap-4 hover:border-emerald-500/25 transition-all relative overflow-hidden group">
                        <div class="w-10 h-10 rounded-xl bg-onyx-950 dark:bg-white text-white dark:text-black flex items-center justify-center shrink-0">
                            <i data-lucide="package" class="w-5 h-5"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[9px] font-black uppercase tracking-wider text-onyx-400">Total Assets</p>
                            <h3 class="text-xl font-black tracking-tight mt-0.5" x-text="totalItems.toLocaleString('id-ID')"></h3>
                        </div>
                    </div>

                    <!-- Inventory Volume -->
                    <div class="zen-glass squircle p-5 flex items-center gap-4 hover:border-emerald-500/25 transition-all relative overflow-hidden group">
                        <div class="w-10 h-10 rounded-xl bg-onyx-950 dark:bg-white text-white dark:text-black flex items-center justify-center shrink-0">
                            <i data-lucide="layers" class="w-5 h-5"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[9px] font-black uppercase tracking-wider text-onyx-400">Inventory Volume</p>
                            <h3 class="text-xl font-black tracking-tight mt-0.5" x-text="totalStock.toLocaleString('id-ID')"></h3>
                        </div>
                    </div>

                    <!-- Market Value -->
                    <div class="zen-glass squircle p-5 flex items-center gap-4 hover:border-emerald-500/25 transition-all relative overflow-hidden group">
                        <div class="w-10 h-10 rounded-xl bg-onyx-950 dark:bg-white text-white dark:text-black flex items-center justify-center shrink-0">
                            <i data-lucide="wallet" class="w-5 h-5"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[9px] font-black uppercase tracking-wider text-onyx-400">Market Value</p>
                            <h3 class="text-sm font-black tracking-tight mt-0.5 truncate" :title="'Rp ' + totalValue.toLocaleString('id-ID')">
                                Rp <span x-text="totalValue.toLocaleString('id-ID')"></span>
                            </h3>
                        </div>
                    </div>

                    <!-- Restock Alerts -->
                    <div class="zen-glass squircle p-5 flex items-center gap-4 hover:border-emerald-500/25 transition-all relative overflow-hidden group">
                        <div class="w-10 h-10 rounded-xl bg-rose-505 bg-rose-500 text-white flex items-center justify-center shrink-0">
                            <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[9px] font-black uppercase tracking-wider text-onyx-400">Restock Alerts</p>
                            <h3 class="text-xl font-black text-rose-500 tracking-tight mt-0.5" x-text="lowStockCount.toLocaleString('id-ID')"></h3>
                        </div>
                    </div>

                </div>

                <!-- Stock Alerts Vertical List (col-span-4) -->
                <div class="xl:col-span-3">
                    <div class="zen-glass squircle p-5 flex flex-col justify-between h-full">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <h3 class="text-[9px] font-black uppercase tracking-wider text-onyx-400">Stock Alerts</h3>
                                <div class="flex items-center gap-1">
                                    <div class="flex items-center gap-1 px-1.5 py-0.5 rounded bg-rose-500/10 border border-rose-500/20 text-[8px] font-black text-rose-500 uppercase tracking-widest">
                                        <span>Alert</span>
                                        <div class="w-1 h-1 rounded-full bg-rose-500 animate-ping"></div>
                                    </div>
                                    <div class="flex items-center gap-1 px-1.5 py-0.5 rounded bg-orange-500/10 border border-orange-500/20 text-[8px] font-black text-orange-500 uppercase tracking-widest">
                                        <span>High</span>
                                        <div class="w-1 h-1 rounded-full bg-orange-500"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="pt-2.5 border-t border-white/5 mt-2.5 flex justify-between items-center text-[8px] font-black uppercase tracking-widest text-onyx-400 hover:text-white transition-colors cursor-pointer">
                            <span>Filtered View</span>
                            <span>&rarr;</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Advanced Intelligence Suite (Line + Bar Chart) -->
            <div class="zen-glass squircle p-6 flex flex-col gap-6 relative group/chart">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div>
                        <h3 class="text-base font-black tracking-tighter italic uppercase">Advanced Intelligence Suite</h3>
                        <p class="text-[9px] font-black uppercase tracking-[0.2em] text-onyx-400">7-Day Transaction Flux • Scrub to Reveal Intel</p>
                    </div>
                    <div class="flex items-center gap-6">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <span class="text-[9px] font-black uppercase tracking-widest text-onyx-400 italic">Inbound</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                            <span class="text-[9px] font-black uppercase tracking-widest text-onyx-400 italic">Outbound</span>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <!-- Line Chart -->
                    <div class="lg:col-span-8 h-72 relative">
                        <canvas id="transactionChart"></canvas>
                    </div>
                    
                    <!-- Category Bar Chart -->
                    <div class="lg:col-span-4 h-72 relative border-l border-black/5 dark:border-white/5 pl-4">
                        <canvas id="categoryBarChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Allocation & Historical Trend Row -->
            <div class="zen-glass squircle p-6 flex flex-col gap-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-black tracking-tighter italic uppercase">Allocation</h3>
                    <span class="text-[9px] font-black uppercase tracking-widest text-onyx-400">All assets</span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Doughnut Chart -->
                    <div class="h-56 relative flex items-center justify-center">
                        <canvas id="allocationChart"></canvas>
                    </div>
                    
                    <!-- Allocation List & Micro Chart -->
                    <div class="flex flex-col justify-between">
                        <div class="space-y-3">
                            <p class="text-[9px] font-black text-onyx-400 uppercase tracking-widest italic">Top Categories</p>
                            <div class="space-y-2.5">
                                @foreach($categoryAllocations->take(4) as $cat)
                                    <div class="flex items-center justify-between text-xs font-black uppercase">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full" style="background-color: {{ ['#ffffff', '#C4A47C', '#1E3F20', '#6366f1'][$loop->index % 4] }}"></span>
                                            <span class="text-onyx-400 truncate max-w-[150px]">{{ $cat['name'] }}</span>
                                        </div>
                                        <div class="flex items-center gap-4 text-right">
                                            <span>{{ $cat['stock'] }}</span>
                                            <span class="text-emerald-500 font-normal">{{ $totalStock > 0 ? number_format(($cat['stock'] / $totalStock) * 100, 1) : 0 }}%</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Micro Historical Trend Sparkline -->
                        <div class="pt-4 border-t border-black/5 dark:border-white/5 mt-4 flex items-center justify-between">
                            <div>
                                <p class="text-[8px] font-black uppercase text-onyx-400 tracking-widest">Historical Trend</p>
                                <p class="text-[9px] font-black uppercase text-onyx-500 tracking-widest mt-1">{{ $histTrendLabelStr }}</p>
                            </div>
                            <div class="w-32 h-10 relative">
                                <canvas id="historicalTrendChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- Drill-Down Overlay -->
    <template x-if="showDrillDown">
        <div class="fixed inset-0 z-[2500] flex items-center justify-center p-6 animate-in fade-in duration-300">
            <div class="fixed inset-0 bg-black/80 backdrop-blur-md" @click="showDrillDown = false"></div>
            <div class="w-full max-w-xl zen-glass squircle p-8 relative z-10 shadow-[0_50px_100px_rgba(0,0,0,0.5)]">
                <div class="flex justify-between items-start mb-8">
                    <div>
                        <p class="text-[9px] font-black text-onyx-400 uppercase tracking-[0.4em] italic">Temporal Intel</p>
                        <h4 class="text-3xl font-black italic tracking-tighter uppercase" x-text="selectedDate"></h4>
                    </div>
                    <button @click="showDrillDown = false" class="w-10 h-10 rounded-2xl hover:bg-black/5 dark:hover:bg-white/5 flex items-center justify-center transition-all border-none cursor-pointer text-onyx-400 hover:text-white">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                
                <div class="grid grid-cols-2 gap-6">
                    <div class="p-6 rounded-2xl bg-emerald-500/10 border border-emerald-500/20">
                        <p class="text-[9px] font-black text-emerald-500 uppercase tracking-widest">Inbound Flux</p>
                        <p class="text-3xl font-black italic mt-1" x-text="drillDownData.in"></p>
                    </div>
                    <div class="p-6 rounded-2xl bg-rose-500/10 border border-rose-500/20">
                        <p class="text-[9px] font-black text-rose-500 uppercase tracking-widest">Outbound Flux</p>
                        <p class="text-3xl font-black italic mt-1" x-text="drillDownData.out"></p>
                    </div>
                </div>

                <div class="mt-8 pt-8 border-t border-black/5 dark:border-white/5 flex justify-end">
                    <a href="{{ route('transactions.index') }}" class="px-6 py-3.5 rounded-xl bg-onyx-950 dark:bg-white text-white dark:text-black text-[9px] font-black uppercase tracking-widest shadow-2xl">Full Audit Trail</a>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection

@push('scripts')
<script>
    let transChart, allocChart, catBarChart, forecastLine, histTrendLine;
    
    function initChart(dark) {
        const transCtx = document.getElementById('transactionChart')?.getContext('2d');
        const allocCtx = document.getElementById('allocationChart')?.getContext('2d');
        const catBarCtx = document.getElementById('categoryBarChart')?.getContext('2d');
        const forecastCtx = document.getElementById('forecastSparkline')?.getContext('2d');
        const histTrendCtx = document.getElementById('historicalTrendChart')?.getContext('2d');

        if(transChart) transChart.destroy();
        if(allocChart) allocChart.destroy();
        if(catBarChart) catBarChart.destroy();
        if(forecastLine) forecastLine.destroy();
        if(histTrendLine) histTrendLine.destroy();

        const gridColor = dark ? 'rgba(255,255,255,0.02)' : 'rgba(0,0,0,0.02)';
        const labelColor = dark ? '#71717a' : '#080808';

        // 1. Transaction Pulse Line Chart with Premium Gradients
        if(transCtx) {
            const inboundGrad = transCtx.createLinearGradient(0, 0, 0, 300);
            inboundGrad.addColorStop(0, 'rgba(16, 185, 129, 0.25)');
            inboundGrad.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

            const outboundGrad = transCtx.createLinearGradient(0, 0, 0, 300);
            outboundGrad.addColorStop(0, 'rgba(239, 68, 68, 0.25)');
            outboundGrad.addColorStop(1, 'rgba(239, 68, 68, 0.0)');

            transChart = new Chart(transCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($last7Days->pluck('date')) !!},
                    datasets: [
                        {
                            label: 'In',
                            data: {!! json_encode($last7Days->pluck('in')) !!},
                            borderColor: '#10b981',
                            borderWidth: 3,
                            tension: 0.4,
                            pointRadius: 3,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#10b981',
                            pointBorderColor: dark ? '#080808' : '#ffffff',
                            pointBorderWidth: 1.5,
                            fill: true,
                            backgroundColor: inboundGrad
                        },
                        {
                            label: 'Out',
                            data: {!! json_encode($last7Days->pluck('out')) !!},
                            borderColor: '#ef4444',
                            borderWidth: 3,
                            tension: 0.4,
                            pointRadius: 3,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#ef4444',
                            pointBorderColor: dark ? '#080808' : '#ffffff',
                            pointBorderWidth: 1.5,
                            fill: true,
                            backgroundColor: outboundGrad
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    onClick: (e, elements) => {
                        if (elements.length > 0) {
                            const index = elements[0].index;
                            const label = transChart.data.labels[index];
                            const inVal = transChart.data.datasets[0].data[index];
                            const outVal = transChart.data.datasets[1].data[index];
                            window.dispatchEvent(new CustomEvent('open-drilldown', {
                                detail: { date: label, in: inVal, out: outVal }
                            }));
                        }
                    },
                    plugins: { 
                        legend: { display: false }, 
                        tooltip: { enabled: true } 
                    },
                    scales: {
                        y: { grid: { color: gridColor }, ticks: { color: labelColor, font: { weight: '800', size: 8 } } },
                        x: { grid: { display: false }, ticks: { color: labelColor, font: { weight: '800', size: 8 } } }
                    }
                }
            });
        }

        // 2. Category Bar Chart with Premium Gradient
        if(catBarCtx) {
            const barGrad = catBarCtx.createLinearGradient(0, 0, 0, 300);
            barGrad.addColorStop(0, '#10b981');
            barGrad.addColorStop(1, '#059669');

            catBarChart = new Chart(catBarCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($categoryAllocations->pluck('name')) !!},
                    datasets: [{
                        label: 'Stock',
                        data: {!! json_encode($categoryAllocations->pluck('stock')) !!},
                        backgroundColor: barGrad,
                        borderRadius: 4,
                        maxBarThickness: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { grid: { color: gridColor }, ticks: { color: labelColor, font: { weight: '800', size: 8 } } },
                        x: { grid: { display: false }, ticks: { color: labelColor, font: { weight: '800', size: 8 } } }
                    }
                }
            });
        }

        // 3. Allocation Doughnut
        if(allocCtx) {
            const baseColors = [dark ? '#ffffff' : '#080808', '#C4A47C', '#1E3F20', '#6366f1', '#10b981', '#f43f5e', '#eab308', '#a855f7'];
            const chartColors = {!! json_encode($categoryAllocations) !!}.map((_, i) => baseColors[i % baseColors.length]);

            allocChart = new Chart(allocCtx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($categoryAllocations->pluck('name')) !!},
                    datasets: [{
                        data: {!! json_encode($categoryAllocations->pluck('stock')) !!},
                        backgroundColor: chartColors,
                        borderWidth: 4,
                        borderColor: dark ? '#0a0a0a' : '#ffffff',
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '80%',
                    plugins: { 
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: dark ? '#ffffff' : '#080808',
                            titleColor: dark ? '#000' : '#fff',
                            bodyColor: dark ? '#000' : '#fff',
                            padding: 10,
                            cornerRadius: 8,
                            displayColors: false
                        }
                    }
                }
            });
        }

        // 4. Forecast Sparkline Chart (Micro) with Area Gradient
        if(forecastCtx) {
            const forecastGrad = forecastCtx.createLinearGradient(0, 0, 0, 50);
            forecastGrad.addColorStop(0, 'rgba(16, 185, 129, 0.2)');
            forecastGrad.addColorStop(1, 'rgba(16, 185, 129, 0)');

            forecastLine = new Chart(forecastCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode(collect($last7Days)->pluck('date')) !!},
                    datasets: [{
                        data: {!! json_encode($forecastData) !!},
                        borderColor: '#10b981',
                        borderWidth: 1.5,
                        tension: 0.4,
                        pointRadius: 0,
                        fill: true,
                        backgroundColor: forecastGrad
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false }, tooltip: { enabled: false } },
                    scales: {
                        y: { display: false },
                        x: { display: false }
                    }
                }
            });
        }

        // 5. Historical Trend Sparkline Chart with Area Gradient
        if(histTrendCtx) {
            const histGrad = histTrendCtx.createLinearGradient(0, 0, 0, 40);
            histGrad.addColorStop(0, dark ? 'rgba(255, 255, 255, 0.15)' : 'rgba(8, 8, 8, 0.15)');
            histGrad.addColorStop(1, 'rgba(0, 0, 0, 0)');

            histTrendLine = new Chart(histTrendCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($historicalTrendLabels) !!},
                    datasets: [{
                        data: {!! json_encode($historicalTrendData) !!},
                        borderColor: dark ? '#ffffff' : '#080808',
                        borderWidth: 1.5,
                        tension: 0.4,
                        pointRadius: 0,
                        fill: true,
                        backgroundColor: histGrad
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false }, tooltip: { enabled: false } },
                    scales: {
                        y: { display: false },
                        x: { display: false }
                    }
                }
            });
        }
    }

    window.addEventListener('open-drilldown', (e) => {
        const alpine = document.querySelector('[x-data]').__x.$data;
        alpine.openDrillDown(e.detail.date, e.detail.in, e.detail.out);
    });

    document.addEventListener('DOMContentLoaded', () => initChart(document.documentElement.classList.contains('dark')));
</script>
@endpush
