@extends('layouts')

@section('page_title', 'Transactions')

@section('breadcrumb')
    <span class="text-onyx-400">WORKFLOW</span>
    <i data-lucide="chevron-right" class="w-3 h-3 text-onyx-300"></i>
    <span class="text-onyx-950 dark:text-white">TRANSACTIONS</span>
@endsection

@section('content')
<div class="space-y-10 animate-in fade-in slide-in-from-bottom-4 duration-700">
    
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="text-5xl lg:text-7xl font-black tracking-tighter italic leading-none uppercase">Flux</h1>
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-onyx-400 mt-2">Real-time Inventory Transaction Registry</p>
        </div>
        <button @click="$dispatch('open-modal', 'add-transaction')" class="inline-flex items-center gap-3 bg-onyx-950 dark:bg-white text-white dark:text-black px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] italic hover:scale-105 transition-all shadow-2xl">
            <i data-lucide="arrow-left-right" class="w-4 h-4"></i>
            Record Flux
        </button>
    </div>

    <!-- Transaction Table -->
    <div class="zen-glass squircle overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[10px] font-black text-onyx-400 uppercase tracking-[0.3em] border-b border-black/5 dark:border-white/5 italic">
                        <th class="px-10 py-6">Timestamp</th>
                        <th class="px-10 py-6">Identity</th>
                        <th class="px-10 py-6 text-center">Protocol</th>
                        <th class="px-10 py-6 text-center">Volume</th>
                        <th class="px-10 py-6">Operator</th>
                        <th class="px-10 py-6 text-right">Reference</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5 dark:divide-white/5">
                    @foreach($transactions as $transaction)
                    <tr class="hover:bg-black/5 dark:hover:bg-white/5 transition-all duration-500 group">
                        <td class="px-10 py-8">
                            <div class="text-xs font-black uppercase tracking-tight">{{ $transaction->created_at->format('d M Y') }}</div>
                            <div class="text-[9px] text-onyx-400 font-black uppercase tracking-widest mt-1">{{ $transaction->created_at->format('H:i') }}</div>
                        </td>
                        <td class="px-10 py-8">
                            <div class="font-black text-sm uppercase tracking-tight text-onyx-950 dark:text-white">{{ $transaction->item->nama_barang }}</div>
                            <div class="text-[9px] text-onyx-400 font-black uppercase tracking-widest mt-1">{{ $transaction->item->kode_barang }}</div>
                        </td>
                        <td class="px-10 py-8 text-center">
                            @if($transaction->type == 'in')
                            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                                <i data-lucide="arrow-down-left" class="w-3 h-3"></i>
                                Inbound
                            </span>
                            @else
                            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest bg-rose-500/10 text-rose-500 border border-rose-500/20">
                                <i data-lucide="arrow-up-right" class="w-3 h-3"></i>
                                Outbound
                            </span>
                            @endif
                        </td>
                        <td class="px-10 py-8 text-center">
                            <span class="text-sm font-black italic {{ $transaction->type == 'in' ? 'text-emerald-500' : 'text-rose-500' }}">
                                {{ $transaction->type == 'in' ? '+' : '-' }}{{ $transaction->quantity }}
                            </span>
                        </td>
                        <td class="px-10 py-8">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-onyx-950 dark:bg-white text-white dark:text-black flex items-center justify-center text-[10px] font-black shadow-lg">
                                    {{ strtoupper(substr($transaction->user->name ?? 'S', 0, 1)) }}
                                </div>
                                <span class="text-[10px] font-black uppercase tracking-widest text-onyx-400">{{ $transaction->user->name ?? 'System' }}</span>
                            </div>
                        </td>
                        <td class="px-10 py-8 text-right">
                            <div class="text-[10px] font-black uppercase tracking-tight text-onyx-400">
                                @if($transaction->customer)
                                    <span class="text-indigo-500">@ {{ $transaction->customer->name }}</span>
                                @endif
                                <span class="ml-2">{{ $transaction->note ?: 'No specific ref' }}</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Transaction Modal -->
    <x-modal name="add-transaction" focusable>
        <div x-data="{ type: 'in' }" class="p-10 space-y-10">
            <div>
                <h3 class="text-4xl font-black italic tracking-tighter uppercase" x-text="type === 'in' ? 'Inbound Flux' : 'Outbound Flux'"></h3>
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-onyx-400 mt-2">Protocol: Manual Item Adjustment</p>
            </div>

            <form action="{{ route('transactions.store') }}" method="POST" class="space-y-8">
                @csrf
                <div class="grid grid-cols-2 gap-4 p-2 zen-glass rounded-2xl">
                    <button type="button" @click="type = 'in'" :class="type === 'in' ? 'bg-onyx-950 dark:bg-white text-white dark:text-black shadow-xl' : 'text-onyx-400'" class="py-4 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">IN (MASUK)</button>
                    <button type="button" @click="type = 'out'" :class="type === 'out' ? 'bg-onyx-950 dark:bg-white text-white dark:text-black shadow-xl' : 'text-onyx-400'" class="py-4 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">OUT (KELUAR)</button>
                    <input type="hidden" name="type" :value="type">
                </div>

                <div class="space-y-4">
                    <label class="text-[10px] font-black text-onyx-400 uppercase tracking-[0.4em] ml-2">Item Resource</label>
                    <select name="item_id" required class="w-full zen-glass border-black/5 dark:border-white/5 rounded-2xl px-8 py-5 text-sm font-black uppercase tracking-tight focus:ring-0 focus:border-onyx-950 dark:focus:border-white outline-none appearance-none transition-all">
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" class="bg-onyx-900 text-white font-black">{{ $item->nama_barang }} (STOCK: {{ $item->stok_barang }})</option>
                        @endforeach
                    </select>
                </div>

                <div x-show="type === 'out'" x-transition class="space-y-4">
                    <label class="text-[10px] font-black text-onyx-400 uppercase tracking-[0.4em] ml-2">Customer Entity (Optional)</label>
                    <select name="customer_id" class="w-full zen-glass border-black/5 dark:border-white/5 rounded-2xl px-8 py-5 text-sm font-black uppercase tracking-tight focus:ring-0 focus:border-onyx-950 dark:focus:border-white outline-none appearance-none transition-all">
                        <option value="" class="bg-onyx-900 text-onyx-500">-- SELECT CUSTOMER --</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" class="bg-onyx-900 text-white font-black">{{ $customer->name }} ({{ $customer->category->name }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-onyx-400 uppercase tracking-[0.4em] ml-2">Volume</label>
                        <x-text-input name="quantity" type="number" required placeholder="0" />
                    </div>
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-onyx-400 uppercase tracking-[0.4em] ml-2">Reference Note</label>
                        <x-text-input name="note" type="text" placeholder="REF_CODE_001" />
                    </div>
                </div>

                <div class="flex items-center justify-end pt-4 gap-4">
                    <x-secondary-button @click="$dispatch('close')">Cancel</x-secondary-button>
                    <x-primary-button>Execute Transaction</x-primary-button>
                </div>
            </form>
        </div>
    </x-modal>
</div>
@endsection
