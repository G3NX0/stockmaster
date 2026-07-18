@extends('layouts')

@section('page_title', 'Expiring Alerts')

@section('breadcrumb')
    <span class="text-onyx-400">INTELLIGENCE</span>
    <i data-lucide="chevron-right" class="w-3 h-3 text-onyx-300"></i>
    <span class="text-onyx-950 dark:text-white">EXPIRING</span>
@endsection

@section('content')
<div class="mb-8 flex justify-between items-end">
    <div>
        <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Expiring Items Dashboard</h1>
        <p class="mt-1 text-sm text-slate-500">Monitor batches approaching their expiration date within the next 90 days.</p>
    </div>
    <div class="flex gap-2">
        <span class="px-4 py-2 rounded-xl bg-rose-500/10 text-rose-500 text-xs font-bold border border-rose-500/20 flex items-center gap-2">
            <div class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></div>
            Live Tracking Enabled
        </span>
    </div>
</div>

@if($batches->isEmpty())
<div class="glass rounded-[2.5rem] p-20 text-center border border-white/50">
    <div class="w-24 h-24 bg-indigo-500/10 rounded-full flex items-center justify-center mx-auto mb-6">
        <i data-lucide="shield-check" class="w-12 h-12 text-indigo-600"></i>
    </div>
    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">No Expiring Items Found</h3>
    <p class="text-slate-500 max-w-sm mx-auto">All your inventory batches are currently within their safe usage period.</p>
</div>
@else
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($batches as $batch)
    @php
        $daysToExpiry = now()->diffInDays($batch->expiry_date, false);
        $urgencyClass = $daysToExpiry <= 30 ? 'bg-rose-500/10 text-rose-500 border-rose-500/20' : 'bg-amber-500/10 text-amber-500 border-amber-500/20';
        $iconClass = $daysToExpiry <= 30 ? 'text-rose-500' : 'text-amber-500';
    @endphp
    <div class="glass rounded-3xl border border-white/40 dark:border-white/5 overflow-hidden group hover:shadow-2xl hover:shadow-indigo-500/10 transition-all duration-500">
        <div class="p-6">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 rounded-2xl bg-slate-100 dark:bg-white/5">
                    <i data-lucide="package" class="w-6 h-6 text-slate-600 dark:text-slate-400"></i>
                </div>
                <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider {{ $urgencyClass }} border">
                    {{ $daysToExpiry <= 0 ? 'Expired' : $daysToExpiry . ' Days Left' }}
                </span>
            </div>
            
            <h3 class="font-bold text-slate-900 dark:text-white text-lg mb-1">{{ $batch->item->nama_barang }}</h3>
            <p class="text-slate-500 text-xs font-medium flex items-center gap-1 mb-4">
                <i data-lucide="hash" class="w-3 h-3"></i>
                Batch: {{ $batch->batch_number }}
            </p>

            <div class="space-y-3 pt-4 border-t border-slate-100 dark:border-white/5">
                <div class="flex justify-between items-center">
                    <span class="text-xs text-slate-400">Expiry Date</span>
                    <span class="text-xs font-bold text-slate-900 dark:text-white">{{ $batch->expiry_date->format('d M Y') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-slate-400">Quantity</span>
                    <span class="text-xs font-bold text-slate-900 dark:text-white">{{ $batch->quantity }} {{ $batch->item->unit->symbol ?? '' }}</span>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100 dark:border-white/5">
                <a href="{{ route('items.show', $batch->item->id) }}" class="w-full flex items-center justify-center gap-2 py-3 rounded-xl bg-slate-900 dark:bg-white/5 text-white dark:text-slate-300 text-xs font-bold hover:bg-indigo-600 dark:hover:bg-indigo-600 transition-colors">
                    Manage Inventory
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>
        </div>
        @if($daysToExpiry <= 30)
        <div class="h-1.5 w-full bg-rose-500"></div>
        @else
        <div class="h-1.5 w-full bg-amber-500"></div>
        @endif
    </div>
    @endforeach
</div>
@endif
@endsection
