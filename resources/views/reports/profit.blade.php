@extends('layouts')

@section('page_title', 'Profit')

@section('breadcrumb')
    <span class="text-onyx-400">INTELLIGENCE</span>
    <i data-lucide="chevron-right" class="w-3 h-3 text-onyx-300"></i>
    <span class="text-onyx-950 dark:text-white">PROFIT</span>
@endsection

@section('content')
<div class="space-y-10 animate-in fade-in slide-in-from-bottom-4 duration-700">
    
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="text-5xl lg:text-7xl font-black tracking-tighter italic leading-none uppercase">Revenue</h1>
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-onyx-400 mt-2">Inventory Profitability & Margin Intelligence</p>
        </div>
    </div>

    <!-- Profit Bento Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Potential Profit -->
        <div class="zen-glass squircle p-8 group relative overflow-hidden">
            <div class="relative z-10 space-y-6">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500 text-white flex items-center justify-center shadow-xl">
                    <i data-lucide="trending-up" class="w-6 h-6"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.3em] text-onyx-400 mb-1">Potential Profit</p>
                    <h3 class="text-3xl font-black tracking-tighter italic leading-none">Rp {{ number_format($items->sum(fn($i) => $i->profit_potential), 0, ',', '.') }}</h3>
                </div>
            </div>
            <i data-lucide="trending-up" class="absolute -right-4 -bottom-4 w-32 h-32 opacity-[0.03] dark:opacity-[0.05]"></i>
        </div>

        <!-- Average Margin -->
        <div class="zen-glass squircle p-8 group relative overflow-hidden">
            <div class="relative z-10 space-y-6">
                <div class="w-12 h-12 rounded-2xl bg-indigo-500 text-white flex items-center justify-center shadow-xl">
                    <i data-lucide="pie-chart" class="w-6 h-6"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.3em] text-onyx-400 mb-1">Avg Margin</p>
                    <h3 class="text-4xl font-black tracking-tighter italic leading-none">{{ number_format($items->avg(fn($i) => $i->profit_margin), 1) }}%</h3>
                </div>
            </div>
            <div class="absolute inset-x-8 bottom-8">
                <div class="w-full h-1 bg-black/5 dark:bg-white/5 rounded-full overflow-hidden">
                    <div class="h-full bg-indigo-500" style="width: {{ $items->avg(fn($i) => $i->profit_margin) }}%"></div>
                </div>
            </div>
        </div>

        <!-- Market Value -->
        <div class="zen-glass squircle p-8 group relative overflow-hidden">
            <div class="relative z-10 space-y-6">
                <div class="w-12 h-12 rounded-2xl bg-onyx-950 dark:bg-white text-white dark:text-black flex items-center justify-center shadow-xl">
                    <i data-lucide="activity" class="w-6 h-6"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.3em] text-onyx-400 mb-1">Market Value (Sell)</p>
                    <h3 class="text-3xl font-black tracking-tighter italic leading-none">Rp {{ number_format($items->sum(fn($i) => $i->selling_price * $i->stok_barang), 0, ',', '.') }}</h3>
                </div>
            </div>
            <i data-lucide="activity" class="absolute -right-4 -bottom-4 w-32 h-32 opacity-[0.03] dark:opacity-[0.05]"></i>
        </div>
    </div>

    <!-- Charts Engine -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        <div class="zen-glass squircle p-10 flex flex-col gap-10 h-[450px]">
            <div>
                <h3 class="text-2xl font-black tracking-tighter italic uppercase">Top Assets</h3>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-onyx-400">Items with highest profit potential</p>
            </div>
            <div class="flex-1">
                <canvas id="profitChart"></canvas>
            </div>
        </div>
        <div class="zen-glass squircle p-10 flex flex-col gap-10 h-[450px]">
            <div>
                <h3 class="text-2xl font-black tracking-tighter italic uppercase">Margin Flux</h3>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-onyx-400">Profitability by Category</p>
            </div>
            <div class="flex-1 relative">
                <canvas id="marginChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Detailed Ledger -->
    <div class="zen-glass squircle overflow-hidden">
        <div class="px-10 py-8 border-b border-black/5 dark:border-white/5">
            <h3 class="text-2xl font-black tracking-tighter italic uppercase">Profit Ledger</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[10px] font-black text-onyx-400 uppercase tracking-[0.3em] border-b border-black/5 dark:border-white/5 italic">
                        <th class="px-10 py-6">Asset Identity</th>
                        <th class="px-10 py-6">Cost</th>
                        <th class="px-10 py-6">Sell Price</th>
                        <th class="px-10 py-6 text-center">Margin</th>
                        <th class="px-10 py-6 text-right">Potential</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5 dark:divide-white/5">
                    @foreach($items->sortByDesc(fn($i) => $i->profit_potential)->take(10) as $item)
                    <tr class="hover:bg-black/5 dark:hover:bg-white/5 transition-all duration-500 group">
                        <td class="px-10 py-8">
                            <span class="text-sm font-black uppercase tracking-tight text-onyx-950 dark:text-white">{{ $item->nama_barang }}</span>
                        </td>
                        <td class="px-10 py-8 text-xs font-black text-onyx-400 uppercase tracking-widest italic">
                            Rp {{ number_format($item->harga_barang, 0, ',', '.') }}
                        </td>
                        <td class="px-10 py-8 text-xs font-black text-indigo-500 uppercase tracking-widest italic">
                            Rp {{ number_format($item->selling_price, 0, ',', '.') }}
                        </td>
                        <td class="px-10 py-8 text-center">
                            <span class="px-4 py-2 bg-emerald-500/10 text-emerald-500 rounded-xl text-[10px] font-black tracking-widest italic uppercase">
                                {{ number_format($item->profit_margin, 1) }}%
                            </span>
                        </td>
                        <td class="px-10 py-8 text-right font-black text-onyx-950 dark:text-white italic text-sm">
                            Rp {{ number_format($item->profit_potential, 0, ',', '.') }}
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
    let profitChart, marginChart;

    function initChart(dark) {
        const labelColor = dark ? '#71717a' : '#080808';
        const gridColor = dark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';

        // Profit Bar Chart
        const profitCtx = document.getElementById('profitChart').getContext('2d');
        if(profitChart) profitChart.destroy();

        profitChart = new Chart(profitCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($items->sortByDesc(fn($i) => $i->profit_potential)->take(5)->pluck('nama_barang')) !!},
                datasets: [{
                    label: 'Profit Potential',
                    data: {!! json_encode($items->sortByDesc(fn($i) => $i->profit_potential)->take(5)->map(fn($i) => (float)$i->profit_potential)->values()) !!},
                    backgroundColor: dark ? '#6366f1' : '#080808',
                    borderRadius: 12,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: dark ? '#101010' : '#ffffff',
                        titleColor: dark ? '#ffffff' : '#080808',
                        titleFont: { family: 'Inter', size: 12, weight: '900' },
                        bodyFont: { family: 'Inter', size: 11, weight: '700' },
                        cornerRadius: 16,
                        displayColors: false
                    }
                },
                scales: { 
                    y: { grid: { color: gridColor, drawBorder: false }, ticks: { color: labelColor, font: { family: 'Inter', size: 9, weight: '900' } } },
                    x: { grid: { display: false }, ticks: { color: labelColor, font: { family: 'Inter', size: 9, weight: '900' } } }
                }
            }
        });

        @php
            $categoryMargins = $items->groupBy('category.name')->map(fn($group) => $group->avg(fn($i) => $i->profit_margin));
        @endphp

        // Margin Doughnut Chart
        const marginCtx = document.getElementById('marginChart').getContext('2d');
        if(marginChart) marginChart.destroy();

        marginChart = new Chart(marginCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($categoryMargins->keys()) !!},
                datasets: [{
                    data: {!! json_encode($categoryMargins->values()) !!},
                    backgroundColor: [dark ? '#ffffff' : '#080808', '#6366f1', '#10b981', '#f59e0b', '#ef4444'],
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
                            font: { family: 'Inter', size: 9, weight: '900' } 
                        }
                    },
                    tooltip: {
                        backgroundColor: dark ? '#101010' : '#ffffff',
                        titleColor: dark ? '#ffffff' : '#080808',
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
