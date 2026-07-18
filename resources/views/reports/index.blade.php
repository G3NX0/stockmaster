@extends('layouts')

@section('page_title', 'Intelligence')

@section('breadcrumb')
    <span class="text-onyx-400">INTELLIGENCE</span>
    <i data-lucide="chevron-right" class="w-3 h-3 text-onyx-300"></i>
    <span class="text-onyx-950 dark:text-white">HUB</span>
@endsection

@section('content')
<div class="space-y-10 animate-in fade-in slide-in-from-bottom-4 duration-700">
    
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="text-5xl lg:text-7xl font-black tracking-tighter italic leading-none uppercase">Intelligence</h1>
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-onyx-400 mt-2">Deep Insights & Predictive Data Analytics</p>
        </div>
    </div>

    <!-- Intelligence Bento Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <!-- Turnover -->
        <div class="zen-glass squircle p-8 group relative overflow-hidden">
            <div class="relative z-10 space-y-6">
                <div class="w-12 h-12 rounded-2xl bg-indigo-500 text-white flex items-center justify-center shadow-xl">
                    <i data-lucide="refresh-cw" class="w-6 h-6"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-onyx-400 mb-1">Turnover Ratio</p>
                    <h3 class="text-4xl font-black tracking-tighter italic leading-none">{{ number_format($turnoverRatio, 2) }}%</h3>
                </div>
            </div>
            <i data-lucide="refresh-cw" class="absolute -right-4 -bottom-4 w-32 h-32 opacity-[0.03] dark:opacity-[0.05] group-hover:rotate-180 transition-transform duration-1000"></i>
        </div>

        <!-- Monthly Volume -->
        <div class="zen-glass squircle p-8 group relative overflow-hidden">
            <div class="relative z-10 space-y-6">
                <div class="w-12 h-12 rounded-2xl bg-onyx-950 dark:bg-white text-white dark:text-black flex items-center justify-center shadow-xl">
                    <i data-lucide="trending-down" class="w-6 h-6"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-onyx-400 mb-1">Monthly Flux</p>
                    <h3 class="text-4xl font-black tracking-tighter italic leading-none">{{ number_format($totalMonthlyOut) }}</h3>
                </div>
            </div>
            <i data-lucide="trending-down" class="absolute -right-4 -bottom-4 w-32 h-32 opacity-[0.03] dark:opacity-[0.05]"></i>
        </div>

        <!-- AI Forecast Link -->
        <a href="{{ route('reports.forecasting') }}" class="zen-glass squircle p-8 group relative overflow-hidden hover:scale-[1.02] transition-all duration-500 bg-gradient-to-br from-indigo-500/5 to-purple-500/5">
            <div class="relative z-10 space-y-6">
                <div class="w-12 h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shadow-xl group-hover:scale-110 transition-transform">
                    <i data-lucide="sparkles" class="w-6 h-6"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-onyx-400 mb-1">AI Protocol</p>
                    <h3 class="text-2xl font-black tracking-tighter italic leading-none uppercase">Forecast</h3>
                </div>
            </div>
            <i data-lucide="arrow-right" class="absolute right-8 bottom-8 w-6 h-6 text-onyx-200 dark:text-onyx-700 group-hover:translate-x-4 transition-transform"></i>
        </a>

        <!-- Heatmap Link -->
        <a href="{{ route('reports.heatmap') }}" class="zen-glass squircle p-8 group relative overflow-hidden hover:scale-[1.02] transition-all duration-500 bg-gradient-to-br from-emerald-500/5 to-teal-500/5">
            <div class="relative z-10 space-y-6">
                <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shadow-xl group-hover:scale-110 transition-transform">
                    <i data-lucide="layout-grid" class="w-6 h-6"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-onyx-400 mb-1">Spatial Map</p>
                    <h3 class="text-2xl font-black tracking-tighter italic leading-none uppercase">Heatmap</h3>
                </div>
            </div>
            <i data-lucide="arrow-right" class="absolute right-8 bottom-8 w-6 h-6 text-onyx-200 dark:text-onyx-700 group-hover:translate-x-4 transition-transform"></i>
        </a>
    </div>

    <!-- Charts Engine -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        <!-- Transaction Trends -->
        <div class="zen-glass squircle p-10 flex flex-col gap-10">
            <div>
                <h3 class="text-2xl font-black tracking-tighter italic uppercase">Flux Trends</h3>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-onyx-400">Inventory Movement Analytics</p>
            </div>
            <div class="h-80 w-full">
                <canvas id="trendsChart"></canvas>
            </div>
        </div>

        <!-- Category Proportion -->
        <div class="zen-glass squircle p-10 flex flex-col gap-10">
            <div>
                <h3 class="text-2xl font-black tracking-tighter italic uppercase">Proportion</h3>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-onyx-400">Inventory Distribution by Category</p>
            </div>
            <div class="h-80 w-full relative">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Fast Moving Table -->
    <div class="zen-glass squircle overflow-hidden">
        <div class="px-10 py-8 border-b border-black/5 dark:border-white/5 flex items-center justify-between">
            <h3 class="text-2xl font-black tracking-tighter italic uppercase">Velocity Registry</h3>
            <p class="text-[9px] font-black uppercase tracking-widest text-onyx-400 italic">Top Performers</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-[10px] font-black text-onyx-400 uppercase tracking-[0.3em] border-b border-black/5 dark:border-white/5 italic">
                        <th class="px-10 py-6">Asset Identity</th>
                        <th class="px-10 py-6 text-center">Volume Index</th>
                        <th class="px-10 py-6 text-right">Health Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5 dark:divide-white/5">
                    @foreach($topItems as $item)
                    <tr class="group hover:bg-black/5 dark:hover:bg-white/5 transition-all duration-500">
                        <td class="px-10 py-8">
                            <span class="text-sm font-black uppercase tracking-tight text-onyx-950 dark:text-white">{{ $item->item->nama_barang }}</span>
                        </td>
                        <td class="px-10 py-8 text-center">
                            <span class="px-4 py-2 bg-rose-500/10 text-rose-500 rounded-xl text-[10px] font-black tracking-widest italic uppercase">
                                {{ number_format($item->total) }} UNITS
                            </span>
                        </td>
                        <td class="px-10 py-8 text-right">
                            @if($item->item->stok_barang <= ($item->item->min_stock ?? 10))
                            <span class="px-4 py-2 rounded-xl text-[9px] font-black bg-rose-500 text-white uppercase tracking-widest italic shadow-lg shadow-rose-500/20">Critical</span>
                            @else
                            <span class="px-4 py-2 rounded-xl text-[9px] font-black bg-emerald-500 text-white uppercase tracking-widest italic shadow-lg shadow-emerald-500/20">Optimized</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let trendsChart, categoryChart;

    // PRESERVE YOUR PERFECT TOOLTIP POSITIONER
    Chart.Tooltip.positioners.follow = function(items, eventPosition) {
        const pos = Chart.Tooltip.positioners.average(items);
        if (pos === false) return false;
        if (items.length > 0) {
            let nearestItem = items[0];
            let minDist = Math.abs(eventPosition.y - items[0].element.y);
            for (let i = 1; i < items.length; i++) {
                const dist = Math.abs(eventPosition.y - items[i].element.y);
                if (dist < minDist) {
                    minDist = dist;
                    nearestItem = items[i];
                }
            }
            pos.y = nearestItem.element.y;
        }
        return pos;
    };

    function initChart(dark) {
        const labelColor = dark ? '#71717a' : '#080808';
        const gridColor = dark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';

        // Trends Chart Upgrade
        const trendsCtx = document.getElementById('trendsChart').getContext('2d');
        if(trendsChart) trendsChart.destroy();

        const inGrad = trendsCtx.createLinearGradient(0, 0, 0, 400);
        inGrad.addColorStop(0, dark ? 'rgba(99, 102, 241, 0.2)' : 'rgba(99, 102, 241, 0.1)');
        inGrad.addColorStop(1, 'rgba(99, 102, 241, 0)');

        const outGrad = trendsCtx.createLinearGradient(0, 0, 0, 400);
        outGrad.addColorStop(0, dark ? 'rgba(239, 68, 68, 0.2)' : 'rgba(239, 68, 68, 0.1)');
        outGrad.addColorStop(1, 'rgba(239, 68, 68, 0)');

        trendsChart = new Chart(trendsCtx, {
            type: 'line',
            data: {
                labels: @json($chartData['labels']),
                datasets: [
                    {
                        label: 'Inbound',
                        data: @json($chartData['in']),
                        borderColor: '#6366f1',
                        borderWidth: 4,
                        backgroundColor: inGrad,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointBackgroundColor: '#6366f1',
                        pointBorderColor: dark ? '#080808' : '#ffffff',
                        pointBorderWidth: 2,
                        pointHitRadius: 50
                    },
                    {
                        label: 'Outbound',
                        data: @json($chartData['out']),
                        borderColor: '#ef4444',
                        borderWidth: 4,
                        backgroundColor: outGrad,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointBackgroundColor: '#ef4444',
                        pointBorderColor: dark ? '#080808' : '#ffffff',
                        pointBorderWidth: 2,
                        pointHitRadius: 50
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        position: 'follow',
                        backgroundColor: dark ? '#101010' : '#ffffff',
                        titleColor: dark ? '#ffffff' : '#080808',
                        titleFont: { family: 'Inter', size: 14, weight: '900' },
                        bodyColor: dark ? '#a1a1aa' : '#52525b',
                        bodyFont: { family: 'Inter', size: 12, weight: '700' },
                        borderColor: dark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)',
                        borderWidth: 1,
                        padding: 20,
                        cornerRadius: 20,
                        displayColors: false
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: labelColor, font: { family: 'Inter', size: 10, weight: '900' }, padding: 15 } },
                    y: { grid: { color: gridColor, drawBorder: false }, ticks: { color: labelColor, font: { family: 'Inter', size: 10, weight: '900' }, padding: 15 } }
                }
            }
        });

        // Category Chart Upgrade
        const catCtx = document.getElementById('categoryChart').getContext('2d');
        if(categoryChart) categoryChart.destroy();

        categoryChart = new Chart(catCtx, {
            type: 'doughnut',
            data: {
                labels: @json($categoryData['labels']),
                datasets: [{
                    data: @json($categoryData['counts']),
                    backgroundColor: [dark ? '#ffffff' : '#080808', '#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
                    borderWidth: dark ? 2 : 8,
                    borderColor: dark ? '#000000' : '#ffffff',
                    hoverOffset: 30
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '80%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { 
                            color: labelColor, 
                            padding: 30, 
                            usePointStyle: true, 
                            font: { family: 'Inter', size: 10, weight: '900' } 
                        }
                    },
                    tooltip: {
                        backgroundColor: dark ? '#101010' : '#ffffff',
                        titleColor: dark ? '#ffffff' : '#080808',
                        bodyColor: dark ? '#a1a1aa' : '#52525b',
                        padding: 20,
                        cornerRadius: 20,
                        displayColors: false
                    }
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        const isDark = document.documentElement.classList.contains('dark');
        initChart(isDark);
    });
</script>
@endpush
@endsection
