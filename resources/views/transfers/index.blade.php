@extends('layouts')

@section('page_title', 'Transfers')

@section('breadcrumb')
    <span class="text-onyx-400">WORKFLOW</span>
    <i data-lucide="chevron-right" class="w-3 h-3 text-onyx-300"></i>
    <span class="text-onyx-950 dark:text-white">TRANSFERS</span>
@endsection

@section('content')
<div x-data="{ modalOpen: false }" class="space-y-10 animate-in fade-in slide-in-from-bottom-4 duration-700">
    
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="text-5xl lg:text-7xl font-black tracking-tighter italic leading-none uppercase">Transfers</h1>
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-onyx-400 mt-2">Internal Stock Movement & Logistics Flux</p>
        </div>
        <button @click="modalOpen = true; playFeedback()" class="inline-flex items-center gap-3 bg-onyx-950 dark:bg-white text-white dark:text-black px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] italic hover:scale-105 transition-all shadow-2xl">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Initialize Transfer
        </button>
    </div>

    <!-- Data Engine -->
    <div class="zen-glass squircle overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[10px] font-black text-onyx-400 uppercase tracking-[0.3em] border-b border-black/5 dark:border-white/5 italic">
                        <th class="px-10 py-6">Timeline</th>
                        <th class="px-10 py-6">Asset Intelligence</th>
                        <th class="px-10 py-6">Logistics Route</th>
                        <th class="px-10 py-6 text-center">Flux Quantity</th>
                        <th class="px-10 py-6 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5 dark:divide-white/5">
                    @forelse($transfers as $transfer)
                    <tr class="hover:bg-black/5 dark:hover:bg-white/5 transition-all duration-500 group">
                        <td class="px-10 py-8">
                            <div class="flex flex-col">
                                <span class="text-xs font-black italic text-onyx-950 dark:text-white">{{ $transfer->created_at->format('d M Y') }}</span>
                                <span class="text-[9px] font-black text-onyx-400 uppercase tracking-widest mt-1">{{ $transfer->created_at->format('H:i') }} PROTOCOL</span>
                            </div>
                        </td>
                        <td class="px-10 py-8">
                            <div class="flex items-center gap-5">
                                <div class="w-10 h-10 rounded-xl bg-onyx-950 dark:bg-white text-white dark:text-black flex items-center justify-center font-black text-[10px] shadow-lg group-hover:scale-110 transition-transform">
                                    {{ substr($transfer->item->nama_barang, 0, 1) }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-black uppercase tracking-tight text-onyx-950 dark:text-white">{{ $transfer->item->nama_barang }}</span>
                                    <span class="text-[9px] font-black text-onyx-400 uppercase tracking-widest mt-1">{{ $transfer->item->kode_barang }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-10 py-8">
                            <div class="flex items-center gap-4">
                                <span class="px-3 py-1.5 bg-black/5 dark:bg-white/5 rounded-lg text-[9px] font-black uppercase tracking-widest">{{ $transfer->fromWarehouse->name }}</span>
                                <i data-lucide="move-right" class="w-4 h-4 text-onyx-300"></i>
                                <span class="px-3 py-1.5 bg-indigo-500/10 text-indigo-500 rounded-lg text-[9px] font-black uppercase tracking-widest">{{ $transfer->toWarehouse->name }}</span>
                            </div>
                        </td>
                        <td class="px-10 py-8 text-center text-sm font-black italic">
                            {{ $transfer->quantity }}
                        </td>
                        <td class="px-10 py-8 text-right">
                            <span class="inline-flex items-center px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest italic bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                                {{ $transfer->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-10 py-32 text-center">
                            <div class="flex flex-col items-center gap-6 opacity-20">
                                <i data-lucide="database-zap" class="w-16 h-16 text-onyx-400"></i>
                                <div class="space-y-2">
                                    <p class="text-[10px] font-black uppercase tracking-[0.4em] text-onyx-400">Null Transfers Detected</p>
                                    <p class="text-[8px] font-black uppercase tracking-widest text-onyx-300 italic">No logistics flux recorded in history</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Redesigned Zenith Modal -->
    <template x-if="modalOpen">
        <div class="fixed inset-0 z-[2000] flex items-center justify-center p-6 animate-in fade-in duration-300">
            <div class="fixed inset-0 bg-onyx-950/80 backdrop-blur-md" @click="modalOpen = false"></div>
            <div class="w-full max-w-xl zen-glass squircle p-12 relative z-10 shadow-[0_50px_100px_rgba(0,0,0,0.5)]">
                <div class="flex justify-between items-start mb-10">
                    <div>
                        <p class="text-[10px] font-black text-onyx-400 uppercase tracking-[0.4em] italic">Protocol Initiation</p>
                        <h4 class="text-3xl font-black italic tracking-tighter uppercase">New Transfer</h4>
                    </div>
                    <button @click="modalOpen = false" class="w-12 h-12 rounded-2xl hover:bg-black/5 dark:hover:bg-white/5 flex items-center justify-center transition-all">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>

                <form action="{{ route('transfers.store') }}" method="POST" class="space-y-8">
                    @csrf
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-onyx-400 uppercase tracking-[0.3em] ml-2 italic">Select Asset</label>
                        <select name="item_id" required class="w-full zen-glass rounded-2xl px-6 py-5 text-sm font-black uppercase tracking-tight focus:ring-0 focus:border-onyx-950 dark:focus:border-white transition-all appearance-none outline-none">
                            @foreach($items as $item)
                                <option value="{{ $item->id }}" class="bg-white dark:bg-onyx-900">{{ $item->nama_barang }} ({{ $item->stok_barang }} Available)</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-8">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-onyx-400 uppercase tracking-[0.3em] ml-2 italic">Source</label>
                            <select name="from_warehouse_id" required class="w-full zen-glass rounded-2xl px-6 py-5 text-sm font-black uppercase tracking-tight focus:ring-0 transition-all appearance-none outline-none">
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}" class="bg-white dark:bg-onyx-900">{{ $wh->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-onyx-400 uppercase tracking-[0.3em] ml-2 italic">Destination</label>
                            <select name="to_warehouse_id" required class="w-full zen-glass rounded-2xl px-6 py-5 text-sm font-black uppercase tracking-tight focus:ring-0 transition-all appearance-none outline-none">
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}" class="bg-white dark:bg-onyx-900">{{ $wh->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-onyx-400 uppercase tracking-[0.3em] ml-2 italic">Quantity Flux</label>
                        <input type="number" name="quantity" required placeholder="0.00" class="w-full zen-glass rounded-2xl px-6 py-5 text-sm font-black italic focus:ring-0 transition-all outline-none">
                    </div>

                    <div class="pt-6">
                        <button type="submit" @click="playFeedback('success')" class="w-full py-6 rounded-3xl bg-onyx-950 dark:bg-white text-white dark:text-black text-[11px] font-black uppercase tracking-[0.4em] italic shadow-2xl hover:scale-[1.02] active:scale-95 transition-all">
                            Execute Protocol
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>
@endsection
