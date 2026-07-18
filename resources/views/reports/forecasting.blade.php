@extends('layouts')

@section('page_title', 'Predictions')

@section('breadcrumb')
    <span class="text-onyx-400">INTELLIGENCE</span>
    <i data-lucide="chevron-right" class="w-3 h-3 text-onyx-300"></i>
    <span class="text-onyx-950 dark:text-white">FORECASTING</span>
@endsection

@section('content')
<div class="space-y-10 animate-in fade-in slide-in-from-bottom-4 duration-700">
    
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="text-5xl lg:text-7xl font-black tracking-tighter italic leading-none uppercase">Predictions</h1>
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-onyx-400 mt-2">AI-Driven Stock Depletion & Procurement Protocol</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="px-6 py-3 zen-glass rounded-2xl">
                <span class="text-[10px] font-black text-indigo-500 uppercase tracking-widest italic">Algorithm: 30-Day Moving Average</span>
            </div>
        </div>
    </div>

    <!-- Prediction Engine Table -->
    <div class="zen-glass squircle overflow-hidden">
        <div class="px-10 py-8 border-b border-black/5 dark:border-white/5">
            <h3 class="text-2xl font-black tracking-tighter italic uppercase">Depletion Timeline</h3>
        </div>
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[10px] font-black text-onyx-400 uppercase tracking-[0.3em] border-b border-black/5 dark:border-white/5 italic">
                        <th class="px-10 py-6">Asset Identity</th>
                        <th class="px-10 py-6">Current Stock</th>
                        <th class="px-10 py-6 text-center">Daily Flux</th>
                        <th class="px-10 py-6">Intelligence</th>
                        <th class="px-10 py-6">Status</th>
                        <th class="px-10 py-6 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5 dark:divide-white/5">
                    @foreach($items as $item)
                    <tr class="hover:bg-black/5 dark:hover:bg-white/5 transition-all duration-500 group">
                        <td class="px-10 py-8">
                            <div class="flex items-center gap-6">
                                <div class="w-12 h-12 rounded-2xl bg-onyx-950 dark:bg-white text-white dark:text-black flex items-center justify-center font-black text-lg shadow-xl group-hover:scale-110 transition-transform duration-500">
                                    {{ substr($item->nama_barang, 0, 1) }}
                                </div>
                                <div class="flex flex-col">
                                    <p class="font-black text-sm uppercase tracking-tight text-onyx-950 dark:text-white">{{ $item->nama_barang }}</p>
                                    <p class="text-[9px] text-onyx-400 font-black uppercase tracking-widest mt-1">{{ $item->category?->name ?? 'Uncategorized' }} • <span class="text-indigo-500/50">{{ $item->kode_barang }}</span></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-10 py-8">
                            <div class="flex flex-col">
                                <span class="text-sm font-black italic text-onyx-950 dark:text-white">{{ $item->stok_barang }}</span>
                                <span class="text-[9px] font-black text-onyx-400 uppercase tracking-widest">{{ $item->unit?->symbol ?? 'PCS' }}</span>
                            </div>
                        </td>
                        <td class="px-10 py-8 text-center">
                            @php
                                $totalOut = $item->transactions()->where('type', 'out')->where('created_at', '>=', now()->subDays(30))->sum('quantity');
                                $burnRate = round($totalOut / 30, 2);
                            @endphp
                            <div class="inline-flex flex-col items-center px-4 py-2 bg-black/5 dark:bg-white/5 rounded-xl border border-black/5 dark:border-white/5">
                                <span class="text-xs font-black italic text-onyx-950 dark:text-white">{{ $burnRate }}</span>
                                <span class="text-[8px] font-black text-onyx-400 uppercase tracking-widest mt-0.5">/ DAY</span>
                            </div>
                        </td>
                        <td class="px-10 py-8">
                            @if($item->days_left !== null)
                                <div class="flex flex-col">
                                    <span class="text-2xl font-black tracking-tighter italic {{ $item->days_left <= 3 ? 'text-rose-500 animate-pulse' : ($item->days_left <= 7 ? 'text-amber-500' : 'text-emerald-500') }}">
                                        {{ $item->days_left }} {{ Str::plural('DAYS', $item->days_left) }}
                                    </span>
                                    <span class="text-[9px] text-onyx-400 font-black uppercase tracking-widest mt-1 italic">ETA: {{ now()->addDays($item->days_left)->format('d M Y') }}</span>
                                </div>
                            @else
                                <span class="text-[9px] text-onyx-300 dark:text-onyx-700 font-black uppercase tracking-widest italic">Insufficient Data</span>
                            @endif
                        </td>
                        <td class="px-10 py-8">
                            <span class="px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest italic bg-{{ $item->status_color }}-500/10 text-{{ $item->status_color }}-500 border border-{{ $item->status_color }}-500/20">
                                {{ $item->status }}
                            </span>
                        </td>
                        <td class="px-10 py-8 text-right">
                            <a href="{{ route('pos.restock', $item) }}" class="w-12 h-12 flex items-center justify-center rounded-xl bg-onyx-950 dark:bg-white text-white dark:text-black hover:scale-110 active:scale-95 transition-all shadow-2xl">
                                <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- AI Insights Bento -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        <div class="zen-glass squircle p-10 group relative overflow-hidden">
            <h3 class="text-2xl font-black tracking-tighter italic uppercase mb-8 flex items-center gap-4">
                <i data-lucide="lightbulb" class="w-6 h-6 text-amber-500"></i>
                AI Procurement Insights
            </h3>
            <div class="space-y-6">
                @php $criticalCount = $items->where('status', 'CRITICAL')->count(); @endphp
                @if($criticalCount > 0)
                <div class="p-8 rounded-3xl bg-rose-500/10 border border-rose-500/20 flex gap-6 relative z-10">
                    <div class="w-14 h-14 rounded-2xl bg-rose-500/20 flex items-center justify-center shrink-0">
                        <i data-lucide="alert-triangle" class="w-8 h-8 text-rose-500"></i>
                    </div>
                    <div>
                        <p class="font-black text-rose-500 uppercase tracking-widest text-xs mb-2">Critical Action Required</p>
                        <p class="text-sm font-medium text-onyx-600 dark:text-onyx-400 leading-relaxed">
                            System detected {{ $criticalCount }} items reaching zero-stock within 72 hours. Procurement protocol should be initialized immediately.
                        </p>
                    </div>
                </div>
                @else
                <div class="p-8 rounded-3xl bg-emerald-500/10 border border-emerald-500/20 flex gap-6 relative z-10">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-500/20 flex items-center justify-center shrink-0">
                        <i data-lucide="check-circle" class="w-8 h-8 text-emerald-500"></i>
                    </div>
                    <div>
                        <p class="font-black text-emerald-500 uppercase tracking-widest text-xs mb-2">Inventory Health Stable</p>
                        <p class="text-sm font-medium text-onyx-600 dark:text-onyx-400 leading-relaxed">
                            No items are currently in the critical zone. Your supply chain velocity is performing within optimal parameters.
                        </p>
                    </div>
                </div>
                @endif
            </div>
            <i data-lucide="brain" class="absolute -right-4 -bottom-4 w-40 h-40 opacity-[0.03] dark:opacity-[0.05]"></i>
        </div>

        <div class="zen-glass squircle p-10 flex flex-col justify-center items-center text-center relative overflow-hidden group">
             <div class="w-20 h-20 rounded-full bg-indigo-500/10 flex items-center justify-center mb-6 shadow-2xl group-hover:scale-110 transition-transform duration-700">
                <i data-lucide="zap" class="w-10 h-10 text-indigo-500"></i>
             </div>
             <h3 class="text-3xl font-black tracking-tighter italic uppercase">Smart Optimization</h3>
             <p class="text-onyx-400 text-xs font-medium leading-relaxed max-w-sm mt-4">
                Algorithm analyzes 30-day transaction flux to calibrate procurement cycles and identify seasonal volatility patterns.
             </p>
             <i data-lucide="cpu" class="absolute -right-4 -bottom-4 w-40 h-40 opacity-[0.03] dark:opacity-[0.05]"></i>
        </div>
    </div>
</div>
@endsection
