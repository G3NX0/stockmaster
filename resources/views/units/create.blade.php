@extends('layouts')

@section('page_title', 'Create Unit')

@section('breadcrumb')
    <span class="text-onyx-400">INVENTORY</span>
    <i data-lucide="chevron-right" class="w-3 h-3 text-onyx-300"></i>
    <a href="{{ route('units.index') }}" class="hover:text-onyx-950 dark:hover:text-white transition-colors">UNITS</a>
    <i data-lucide="chevron-right" class="w-3 h-3 text-onyx-300"></i>
    <span class="text-onyx-950 dark:text-white uppercase">CREATE</span>
@endsection

@section('content')
<div class="max-w-2xl animate-in fade-in slide-in-from-bottom-4 duration-700">
    <div class="mb-8">
        <h1 class="text-3xl font-black tracking-tighter italic leading-none uppercase">New Measurement</h1>
        <p class="text-[10px] font-black uppercase tracking-[0.3em] text-onyx-400 mt-2">Standardization Registry Protocol</p>
    </div>

    <div class="zen-glass squircle overflow-hidden">
        <form action="{{ route('units.store') }}" method="post" class="p-10 space-y-8">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-3">
                    <label for="name" class="text-[10px] font-black text-onyx-400 uppercase tracking-[0.3em] ml-1">Unit Identity</label>
                    <input type="text" name="name" id="name" required
                        class="block w-full px-6 py-4 bg-white/40 dark:bg-onyx-950/40 backdrop-blur-md border border-onyx-200 dark:border-white/5 rounded-2xl text-onyx-950 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all placeholder:text-onyx-400 font-bold"
                        placeholder="e.g. Kilogram, Pieces">
                </div>
                <div class="space-y-3">
                    <label for="symbol" class="text-[10px] font-black text-onyx-400 uppercase tracking-[0.3em] ml-1">Protocol Symbol</label>
                    <input type="text" name="symbol" id="symbol" required
                        class="block w-full px-6 py-4 bg-white/40 dark:bg-onyx-950/40 backdrop-blur-md border border-onyx-200 dark:border-white/5 rounded-2xl text-onyx-950 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all font-mono uppercase placeholder:text-onyx-400"
                        placeholder="kg, pcs, box">
                </div>
            </div>
            <div class="pt-6 flex items-center gap-4">
                <button type="submit" class="flex-1 inline-flex justify-center items-center gap-3 rounded-2xl bg-onyx-950 dark:bg-white px-6 py-4 text-sm font-black uppercase tracking-widest text-white dark:text-black shadow-xl hover:scale-[1.02] active:scale-[0.98] transition-all">
                    <i data-lucide="save" class="w-5 h-5"></i>
                    Register Unit
                </button>
                <a href="{{ route('units.index') }}" class="px-6 py-4 text-sm font-black uppercase tracking-widest text-onyx-400 hover:text-onyx-950 dark:hover:text-white transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
