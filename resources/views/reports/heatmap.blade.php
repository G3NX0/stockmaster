@extends('layouts')

@section('page_title', 'Heatmap')

@section('breadcrumb')
    <span class="text-onyx-400">INTELLIGENCE</span>
    <i data-lucide="chevron-right" class="w-3 h-3 text-onyx-300"></i>
    <span class="text-onyx-950 dark:text-white">HEATMAP</span>
@endsection

@section('content')
<div class="space-y-10 animate-in fade-in slide-in-from-bottom-4 duration-700">
    
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="text-5xl lg:text-7xl font-black tracking-tighter italic leading-none uppercase">Heatmap</h1>
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-onyx-400 mt-2">Spatial Inventory Distribution & Valuation Map</p>
        </div>
    </div>

    <!-- Heatmap Interface -->
    <div class="zen-glass squircle p-10 min-h-[700px] flex flex-col gap-10">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <h3 class="text-2xl font-black tracking-tighter italic uppercase">Spatial Flux</h3>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-onyx-400 mt-1 italic">Block Size = Total Value (Qty × Price)</p>
            </div>
            <!-- Legend Standard -->
            <div class="flex items-center gap-6 zen-glass px-6 py-3 rounded-2xl">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-onyx-950 dark:bg-white"></span>
                    <span class="text-[8px] font-black uppercase tracking-widest text-onyx-400 italic">High Value</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                    <span class="text-[8px] font-black uppercase tracking-widest text-onyx-400 italic">Mid Value</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span class="text-[8px] font-black uppercase tracking-widest text-onyx-400 italic">Low Value</span>
                </div>
            </div>
        </div>
        
        <div class="flex-grow relative rounded-[2rem] overflow-hidden bg-black/5 dark:bg-white/5 p-4">
            <canvas id="heatmapChart"></canvas>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chartjs-chart-treemap@2.3.0/dist/chartjs-chart-treemap.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const canvas = document.getElementById('heatmapChart');
        const ctx = canvas.getContext('2d');
        const rawData = @json($heatmapData);
        const isDark = document.documentElement.classList.contains('dark');
        
        const chartData = [];
        rawData.forEach(cat => {
            cat.items.forEach(item => {
                chartData.push({
                    category: cat.name,
                    item: item.name,
                    value: item.value,
                    stock: item.stock,
                    unit: item.unit
                });
            });
        });

        // Zenith Controlled Palette
        const palette = ['#080808', '#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'];
        if(isDark) palette[0] = '#ffffff';

        new Chart(ctx, {
            type: 'treemap',
            data: {
                datasets: [{
                    label: 'Inventory Value',
                    tree: chartData,
                    key: 'value',
                    groups: ['category', 'item'],
                    spacing: 4,
                    borderWidth: 0,
                    borderRadius: 16,
                    fontColor: '#fff',
                    borderColor: 'transparent',
                    backgroundColor: (ctx) => {
                        if (ctx.type !== 'data') return 'transparent';
                        const cat = ctx.raw.g || 'Default';
                        let hash = 0;
                        for (let i = 0; i < cat.length; i++) hash = cat.charCodeAt(i) + ((hash << 5) - hash);
                        return palette[Math.abs(hash % palette.length)];
                    },
                    labels: {
                        display: true,
                        formatter: (ctx) => {
                            if (ctx.type !== 'data' || ctx.raw.v < 100000) return ''; // Hide small labels
                            return [ctx.raw.g.toUpperCase(), `RP ${new Intl.NumberFormat('id-ID').format(ctx.raw.v)}`];
                        },
                        font: {
                            family: 'Inter',
                            size: 11,
                            weight: '900'
                        },
                        color: (ctx) => {
                            // Ensure text contrast
                            const bg = ctx.dataset.backgroundColor(ctx);
                            if(bg === '#ffffff') return '#080808';
                            return '#ffffff';
                        }
                    }
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: isDark ? '#101010' : '#ffffff',
                        titleColor: isDark ? '#ffffff' : '#080808',
                        titleFont: { family: 'Inter', size: 14, weight: '900' },
                        bodyColor: isDark ? '#a1a1aa' : '#52525b',
                        bodyFont: { family: 'Inter', size: 12, weight: '700' },
                        borderColor: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)',
                        borderWidth: 1,
                        padding: 20,
                        cornerRadius: 20,
                        displayColors: false,
                        callbacks: {
                            label: (item) => {
                                const data = item.raw._data;
                                return [
                                    `Item: ${data.item}`,
                                    `Category: ${data.category}`,
                                    `Stock: ${data.stock} ${data.unit}`,
                                    `Total Value: Rp ${data.value.toLocaleString('id-ID')}`
                                ];
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
