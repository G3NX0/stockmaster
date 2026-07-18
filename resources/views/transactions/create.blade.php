@extends('layouts')

@section('page_title', 'Record Transaction')

@section('breadcrumb')
    <span class="text-onyx-400">WORKFLOW</span>
    <i data-lucide="chevron-right" class="w-3 h-3 text-onyx-300"></i>
    <a href="{{ route('transactions.index') }}" class="hover:text-onyx-950 dark:hover:text-white transition-colors">TRANSACTIONS</a>
    <i data-lucide="chevron-right" class="w-3 h-3 text-onyx-300"></i>
    <span class="text-onyx-950 dark:text-white uppercase">NEW RECORD</span>
@endsection

@section('content')
<div class="max-w-2xl animate-in fade-in slide-in-from-bottom-4 duration-700">
    <div class="mb-8">
        <h1 class="text-3xl font-black tracking-tighter italic leading-none uppercase">Flux Entry</h1>
        <p class="text-[10px] font-black uppercase tracking-[0.3em] text-onyx-400 mt-2">Inventory Movement Logging Protocol</p>
    </div>

    <div class="zen-glass squircle overflow-hidden">
        <form action="{{ route('transactions.store') }}" method="post" class="p-10 space-y-8">
            @csrf
            
            @if ($errors->any())
            <div class="p-6 bg-rose-500/5 border border-rose-500/10 rounded-2xl">
                <div class="flex items-center gap-3 text-rose-500 mb-3 font-black uppercase tracking-widest text-[10px]">
                    <i data-lucide="alert-circle" class="w-4 h-4"></i>
                    <span>Entry Protocol Error</span>
                </div>
                <ul class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <li class="text-[10px] text-rose-500/70 font-black uppercase tracking-tight">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="space-y-6">
                <div class="space-y-3">
                    <label for="item_id" class="text-[10px] font-black text-onyx-400 uppercase tracking-[0.3em] ml-1">Asset Selection</label>
                    <select name="item_id" id="item_id" required class="block w-full px-6 py-4 bg-white/40 dark:bg-onyx-950/40 backdrop-blur-md border border-onyx-200 dark:border-white/5 rounded-2xl text-onyx-950 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all font-bold appearance-none">
                        <option value="">-- SELECT ASSET --</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" class="bg-white dark:bg-onyx-950 text-onyx-950 dark:text-white font-bold">[{{ $item->kode_barang }}] {{ $item->nama_barang }} (STOCK: {{ $item->stok_barang }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-3">
                        <label for="type" class="text-[10px] font-black text-onyx-400 uppercase tracking-[0.3em] ml-1">Flux Direction</label>
                        <select name="type" id="type" required class="block w-full px-6 py-4 bg-white/40 dark:bg-onyx-950/40 backdrop-blur-md border border-onyx-200 dark:border-white/5 rounded-2xl text-onyx-950 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all font-bold appearance-none">
                            <option value="in" class="bg-white dark:bg-onyx-950 text-emerald-500 font-bold">STOCK ENTRY (+)</option>
                            <option value="out" class="bg-white dark:bg-onyx-950 text-rose-500 font-bold">STOCK RELEASE (-)</option>
                        </select>
                    </div>

                    <div class="space-y-3">
                        <label for="quantity" class="text-[10px] font-black text-onyx-400 uppercase tracking-[0.3em] ml-1">Flux Volume</label>
                        <input type="number" name="quantity" id="quantity" required min="1"
                            class="block w-full px-6 py-4 bg-white/40 dark:bg-onyx-950/40 backdrop-blur-md border border-onyx-200 dark:border-white/5 rounded-2xl text-onyx-950 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all font-black italic"
                            placeholder="0">
                    </div>
                </div>

                <div class="space-y-3">
                    <label for="note" class="text-[10px] font-black text-onyx-400 uppercase tracking-[0.3em] ml-1">Objective Context</label>
                    <textarea name="note" id="note" rows="3"
                        class="block w-full px-6 py-4 bg-white/40 dark:bg-onyx-950/40 backdrop-blur-md border border-onyx-200 dark:border-white/5 rounded-2xl text-onyx-950 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all font-medium"
                        placeholder="Purpose of flux movement..."></textarea>
                </div>
            </div>

            <div class="pt-6 flex items-center gap-4">
                <button type="submit" class="flex-1 inline-flex justify-center items-center gap-3 rounded-2xl bg-onyx-950 dark:bg-white px-6 py-4 text-sm font-black uppercase tracking-widest text-white dark:text-black shadow-xl hover:scale-[1.02] active:scale-[0.98] transition-all">
                    <i data-lucide="save" class="w-5 h-5"></i>
                    Execute Protocol
                </button>
                <a href="{{ route('transactions.index') }}" class="px-6 py-4 text-sm font-black uppercase tracking-widest text-onyx-400 hover:text-onyx-950 dark:hover:text-white transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
