@extends('layouts')

@section('page_title', 'Create Item')

@section('breadcrumb')
    <span class="text-onyx-400">INVENTORY</span>
    <i data-lucide="chevron-right" class="w-3 h-3 text-onyx-300"></i>
    <a href="{{ route('items.index') }}" class="hover:text-onyx-950 dark:hover:text-white transition-colors">ITEMS</a>
    <i data-lucide="chevron-right" class="w-3 h-3 text-onyx-300"></i>
    <span class="text-onyx-950 dark:text-white">CREATE</span>
@endsection

@section('content')
<div class="mb-8">
    <a href="{{ route('items.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors mb-4">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        Kembali ke Daftar
    </a>
    <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Tambah Barang Baru</h1>
    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Isi detail barang di bawah ini untuk menambahkannya ke inventaris.</p>
</div>

<div class="max-w-2xl">
    <div class="glass rounded-[2rem] shadow-2xl border border-white/40 dark:border-white/5 overflow-hidden">
        <form action="{{ route('items.store') }}" method="post" class="p-10 space-y-8">
            @csrf
            <div class="grid grid-cols-1 gap-8">
                <div class="space-y-3">
                    <label for="nama_barang" class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] ml-1">Nama Barang</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                            <i data-lucide="package" class="w-5 h-5"></i>
                        </div>
                        <input type="text" name="nama_barang" id="nama_barang" required
                            class="block w-full pl-12 pr-5 py-4 bg-white/40 dark:bg-slate-900/40 backdrop-blur-md border border-slate-200 dark:border-white/5 rounded-2xl text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all placeholder:text-slate-400"
                            placeholder="Contoh: Laptop MacBook Pro">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-3">
                        <label for="harga_barang" class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] ml-1">Harga Beli (Rp)</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors text-sm font-black">
                                Rp
                            </div>
                            <input type="number" name="harga_barang" id="harga_barang" required
                                class="block w-full pl-12 pr-5 py-4 bg-white/40 dark:bg-slate-900/40 backdrop-blur-md border border-slate-200 dark:border-white/5 rounded-2xl text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                                placeholder="0">
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label for="selling_price" class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] ml-1">Harga Jual (Rp)</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors text-sm font-black">
                                Rp
                            </div>
                            <input type="number" name="selling_price" id="selling_price"
                                class="block w-full pl-12 pr-5 py-4 bg-white/40 dark:bg-slate-900/40 backdrop-blur-md border border-slate-200 dark:border-white/5 rounded-2xl text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all border-dashed"
                                placeholder="Opsional (untuk profit)">
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <label for="stok_barang" class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] ml-1">Stok Barang</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                            <i data-lucide="hash" class="w-5 h-5"></i>
                        </div>
                        <input type="number" name="stok_barang" id="stok_barang" required
                            class="block w-full pl-12 pr-5 py-4 bg-white/40 dark:bg-slate-900/40 backdrop-blur-md border border-slate-200 dark:border-white/5 rounded-2xl text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                            placeholder="0">
                    </div>
                </div>

                <div class="p-6 rounded-3xl bg-slate-100/50 dark:bg-white/5 border border-slate-200 dark:border-white/5" x-data="{ isAsset: false }">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-indigo-500/10 rounded-lg">
                                <i data-lucide="hard-drive" class="w-4 h-4 text-indigo-600"></i>
                            </div>
                            <div>
                                <span class="block text-xs font-black uppercase tracking-widest text-slate-700 dark:text-white">Track as Asset</span>
                                <span class="text-[10px] text-slate-400 font-medium">Enable depreciation tracking for this item</span>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_asset" value="1" class="sr-only peer" x-model="isAsset">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>

                    <div x-show="isAsset" x-transition class="space-y-6 pt-4 border-t border-slate-200 dark:border-white/10">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-1">Purchase Date</label>
                                <input type="date" name="purchase_date" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-1">Useful Life (Months)</label>
                                <input type="number" name="useful_life_months" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm" placeholder="e.g. 24">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-1">Salvage Value (Rp)</label>
                            <input type="number" name="salvage_value" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm" placeholder="Residual value after life ends">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] ml-1">Kategori</label>
                        <div class="relative" x-data="{ open: false, selectedId: '{{ old('category_id') }}', selectedName: 'Pilih Kategori' }">
                            <input type="hidden" name="category_id" :value="selectedId">
                            <button @click="open = !open" @click.away="open = false" type="button" 
                                class="w-full px-5 py-4 text-left bg-white/40 dark:bg-slate-900/40 backdrop-blur-md border border-slate-200 dark:border-white/5 rounded-2xl text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all flex justify-between items-center">
                                <span x-text="selectedName"></span>
                                <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open" x-transition class="absolute z-50 mt-2 w-full glass dark:bg-slate-900 border border-white/20 dark:border-white/10 rounded-2xl shadow-2xl overflow-hidden py-2" style="display: none;">
                                @foreach($categories as $category)
                                <div @click="selectedId = '{{ $category->id }}'; selectedName = '{{ $category->name }}'; open = false" 
                                    class="px-5 py-3 text-sm cursor-pointer hover:bg-indigo-600 hover:text-white transition-colors"
                                    :class="selectedId == '{{ $category->id }}' ? 'bg-indigo-600/20 text-indigo-600' : 'text-slate-600 dark:text-slate-300'">
                                    {{ $category->name }}
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] ml-1">Satuan</label>
                        <div class="relative" x-data="{ open: false, selectedId: '{{ old('unit_id') }}', selectedName: 'Pilih Satuan' }">
                            <input type="hidden" name="unit_id" :value="selectedId">
                            <button @click="open = !open" @click.away="open = false" type="button" 
                                class="w-full px-5 py-4 text-left bg-white/40 dark:bg-slate-900/40 backdrop-blur-md border border-slate-200 dark:border-white/5 rounded-2xl text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all flex justify-between items-center">
                                <span x-text="selectedName"></span>
                                <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open" x-transition class="absolute z-50 mt-2 w-full glass dark:bg-slate-900 border border-white/20 dark:border-white/10 rounded-2xl shadow-2xl overflow-hidden py-2" style="display: none;">
                                @foreach($units as $unit)
                                <div @click="selectedId = '{{ $unit->id }}'; selectedName = '{{ $unit->name }} ({{ $unit->symbol }})'; open = false" 
                                    class="px-5 py-3 text-sm cursor-pointer hover:bg-indigo-600 hover:text-white transition-colors"
                                    :class="selectedId == '{{ $unit->id }}' ? 'bg-indigo-600/20 text-indigo-600' : 'text-slate-600 dark:text-slate-300'">
                                    {{ $unit->name }} ({{ $unit->symbol }})
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] ml-1">Supplier</label>
                        <div class="relative" x-data="{ open: false, selectedId: '{{ old('supplier_id') }}', selectedName: 'Pilih Supplier' }">
                            <input type="hidden" name="supplier_id" :value="selectedId">
                            <button @click="open = !open" @click.away="open = false" type="button" 
                                class="w-full px-5 py-4 text-left bg-white/40 dark:bg-slate-900/40 backdrop-blur-md border border-slate-200 dark:border-white/5 rounded-2xl text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all flex justify-between items-center">
                                <span x-text="selectedName"></span>
                                <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open" x-transition class="absolute z-50 mt-2 w-full glass dark:bg-slate-900 border border-white/20 dark:border-white/10 rounded-2xl shadow-2xl overflow-hidden py-2" style="display: none;">
                                @foreach($suppliers as $supplier)
                                <div @click="selectedId = '{{ $supplier->id }}'; selectedName = '{{ $supplier->name }}'; open = false" 
                                    class="px-5 py-3 text-sm cursor-pointer hover:bg-indigo-600 hover:text-white transition-colors"
                                    :class="selectedId == '{{ $supplier->id }}' ? 'bg-indigo-600/20 text-indigo-600' : 'text-slate-600 dark:text-slate-300'">
                                    {{ $supplier->name }}
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] ml-1">Stok Minimum (Alert)</label>
                        <input type="number" name="min_stock" value="5" required class="block w-full px-5 py-4 bg-white/40 dark:bg-slate-900/40 backdrop-blur-md border border-slate-200 dark:border-white/5 rounded-2xl text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all" placeholder="Misal: 10">
                    </div>
                </div>

                <div class="space-y-3">
                    <label for="kode_barang" class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] ml-1">Kode Barang</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                            <i data-lucide="qr-code" class="w-5 h-5"></i>
                        </div>
                        <input type="text" name="kode_barang" id="kode_barang" required
                            class="block w-full pl-12 pr-5 py-4 bg-white/40 dark:bg-slate-900/40 backdrop-blur-md border border-slate-200 dark:border-white/5 rounded-2xl text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all uppercase font-mono"
                            placeholder="SKU-001">
                    </div>
                </div>
            </div>

            <div class="pt-6 flex items-center gap-4">
                <button type="submit" class="flex-1 inline-flex justify-center items-center gap-3 rounded-2xl bg-indigo-600 px-6 py-4 text-sm font-black uppercase tracking-widest text-white shadow-xl shadow-indigo-600/20 hover:bg-indigo-700 transition-all active:scale-95">
                    <i data-lucide="save" class="w-5 h-5"></i>
                    Simpan Barang
                </button>
                <a href="{{ route('items.index') }}" class="inline-flex items-center gap-3 rounded-2xl bg-white/40 dark:bg-slate-800/40 backdrop-blur-md px-6 py-4 text-sm font-black uppercase tracking-widest text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-white/5 hover:bg-white/60 dark:hover:bg-slate-700/60 transition-all">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
