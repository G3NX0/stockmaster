@extends('layouts')

@section('page_title', 'New Audit')

@section('breadcrumb')
    <span class="text-onyx-400">SYSTEM</span>
    <i data-lucide="chevron-right" class="w-3 h-3 text-onyx-300"></i>
    <a href="{{ route('reconciliations.index') }}" class="hover:text-onyx-950 dark:hover:text-white transition-colors">RECONCILIATION</a>
    <i data-lucide="chevron-right" class="w-3 h-3 text-onyx-300"></i>
    <span class="text-onyx-950 dark:text-white">NEW</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-8">
        <a href="{{ route('reconciliations.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-indigo-600 transition-colors mb-4">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Daftar
        </a>
        <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Audit Stok Baru</h1>
        <p class="mt-1 text-sm text-slate-500">Input hasil perhitungan fisik untuk menyesuaikan stok sistem.</p>
    </div>

    <form action="{{ route('reconciliations.store') }}" method="POST" class="glass border border-white/50 rounded-[2.5rem] p-8 shadow-2xl space-y-6">
        @csrf
        
        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Pilih Barang</label>
            <div class="relative" x-data="{ open: false, selectedId: '{{ $selectedItem->id ?? '' }}', selectedName: '{{ $selectedItem ? ($selectedItem->nama_barang . ' (Sistem: ' . $selectedItem->stok_barang . ')') : 'Pilih barang untuk diaudit...' }}' }">
                <input type="hidden" name="item_id" :value="selectedId" required>
                <button @click="open = !open" @click.away="open = false" type="button" 
                    class="w-full bg-white/50 dark:bg-slate-800/50 border border-slate-200 dark:border-white/10 rounded-2xl px-6 py-4 text-left text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all outline-none flex justify-between items-center">
                    <span x-text="selectedName"></span>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open" x-transition class="absolute z-50 mt-2 w-full glass dark:bg-slate-900 border border-white/20 dark:border-white/10 rounded-2xl shadow-2xl overflow-hidden py-2" style="display: none;">
                    @foreach($items as $item)
                    <div @click="selectedId = '{{ $item->id }}'; selectedName = '{{ $item->nama_barang }} (Sistem: {{ $item->stok_barang }})'; open = false" 
                        class="px-6 py-3 text-sm cursor-pointer hover:bg-indigo-600 hover:text-white transition-colors"
                        :class="selectedId == '{{ $item->id }}' ? 'bg-indigo-600/20 text-indigo-600' : 'text-slate-600 dark:text-slate-300'">
                        {{ $item->nama_barang }} (Sistem: {{ $item->stok_barang }})
                    </div>
                    @endforeach
                </div>
            </div>
            @error('item_id') <p class="mt-2 text-xs text-rose-500 font-bold px-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Stok Fisik (Hasil Hitung)</label>
            <input type="number" name="physical_stock" step="0.01" class="w-full bg-white/50 dark:bg-slate-800/50 border border-slate-200 dark:border-white/10 rounded-2xl px-6 py-4 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all outline-none" placeholder="Masukkan jumlah fisik..." required>
            @error('physical_stock') <p class="mt-2 text-xs text-rose-500 font-bold px-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Alasan Penyesuaian</label>
            <textarea name="reason" rows="3" class="w-full bg-white/50 dark:bg-slate-800/50 border border-slate-200 dark:border-white/10 rounded-2xl px-6 py-4 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all outline-none" placeholder="Contoh: Barang rusak, salah hitung sebelumnya, dsb."></textarea>
            @error('reason') <p class="mt-2 text-xs text-rose-500 font-bold px-1">{{ $message }}</p> @enderror
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full py-5 bg-indigo-600 text-white rounded-[1.5rem] font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-indigo-600/30 hover:bg-indigo-700 hover:-translate-y-1 transition-all active:translate-y-0">
                Konfirmasi & Update Stok
            </button>
            <p class="mt-4 text-[10px] text-center text-slate-400 font-medium px-4">
                <i data-lucide="info" class="w-3 h-3 inline-block mr-1"></i>
                Stok di database akan langsung diperbarui setelah Anda menekan tombol di atas.
            </p>
        </div>
    </form>
</div>
@endsection
