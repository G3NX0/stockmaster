@extends('layouts')

@section('page_title', 'Stock Items')

@section('breadcrumb')
    <span class="text-onyx-400">INVENTORY</span>
    <i data-lucide="chevron-right" class="w-3 h-3 text-onyx-300"></i>
    <span class="text-onyx-950 dark:text-white">ITEMS</span>
@endsection

@section('content')
<div x-data="{ 
    selectedItems: [],
    search: '', 
    filterCategory: '',
    items: {{ $items->map(fn($i) => [
        'id' => $i->id,
        'name' => $i->nama_barang,
        'code' => $i->kode_barang,
        'category' => $i->category?->name ?? 'Uncategorized',
        'price' => $i->harga_barang,
        'stock' => $i->stok_barang,
        'unit' => $i->unit?->symbol ?? 'pcs',
        'prediction' => $i->prediction,
        'showUrl' => route('items.show', $i->id),
        'editUrl' => route('items.edit', $i->id),
        'deleteUrl' => route('items.destroy', $i->id)
    ])->toJson() }},
    toggleSelectAll() {
        if (this.selectedItems.length === this.items.length) {
            this.selectedItems = [];
        } else {
            this.selectedItems = this.items.map(i => i.id);
        }
        playFeedback();
    },
    toggleItem(id) {
        if (this.selectedItems.includes(id)) {
            this.selectedItems = this.selectedItems.filter(i => i !== id);
        } else {
            this.selectedItems.push(id);
        }
        playFeedback();
    }
}" 
x-init="$nextTick(() => lucide.createIcons()); $watch('search', () => $nextTick(() => lucide.createIcons())); $watch('filterCategory', () => $nextTick(() => lucide.createIcons()))"
class="space-y-10 animate-in fade-in slide-in-from-bottom-4 duration-700 relative">
    
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="text-5xl lg:text-7xl font-black tracking-tighter italic leading-none uppercase">Inventory</h1>
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-onyx-400 mt-2">Warehouse Stock Management Protocol</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2 zen-glass p-2 rounded-2xl">
                <a href="{{ route('items.export-pdf') }}" class="w-10 h-10 flex items-center justify-center text-onyx-400 hover:text-rose-500 transition-colors" title="Export PDF">
                    <i data-lucide="file-text" class="w-5 h-5"></i>
                </a>
                <a href="{{ route('items.export-excel') }}" class="w-10 h-10 flex items-center justify-center text-onyx-400 hover:text-emerald-500 transition-colors" title="Export Excel">
                    <i data-lucide="file-spreadsheet" class="w-5 h-5"></i>
                </a>
                <button onclick="document.getElementById('importInput').click()" class="w-10 h-10 flex items-center justify-center text-onyx-400 hover:text-indigo-500 transition-colors" title="Import Excel">
                    <i data-lucide="upload-cloud" class="w-5 h-5"></i>
                </button>
            </div>
            <a href="{{ route('items.create') }}" class="inline-flex items-center gap-3 bg-onyx-950 dark:bg-white text-white dark:text-black px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] italic magnetic-btn shadow-2xl">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Add Item
            </a>
        </div>
    </div>

    <!-- Quick Insights -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="zen-glass squircle p-8 flex items-center gap-6">
            <div class="w-14 h-14 rounded-2xl bg-onyx-950 dark:bg-white text-white dark:text-black flex items-center justify-center shadow-xl">
                <i data-lucide="package" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-onyx-400">SKU Count</p>
                <p class="text-3xl font-black tracking-tighter italic">{{ number_format(count($items)) }}</p>
            </div>
        </div>
        <div class="zen-glass squircle p-8 flex items-center gap-6">
            <div class="w-14 h-14 rounded-2xl bg-emerald-500 text-white flex items-center justify-center shadow-xl">
                <i data-lucide="wallet" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-onyx-400">Valuation</p>
                <p class="text-2xl font-black tracking-tighter italic">Rp {{ number_format($items->sum(fn($i) => $i->harga_barang * $i->stok_barang), 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="zen-glass squircle p-8 flex items-center gap-6">
            <div class="w-14 h-14 rounded-2xl bg-rose-500 text-white flex items-center justify-center shadow-xl">
                <i data-lucide="zap" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-onyx-400">Sync Pulse</p>
                <p class="text-3xl font-black tracking-tighter italic">ACTIVE</p>
            </div>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="relative group">
            <i data-lucide="search" class="absolute left-6 top-1/2 -translate-y-1/2 w-5 h-5 text-onyx-400 group-focus-within:text-onyx-950 dark:group-focus-within:text-white transition-colors"></i>
            <input type="text" x-model="search" placeholder="Search protocol..." 
                class="w-full pl-16 pr-6 py-5 zen-glass rounded-[1.5rem] text-sm font-black tracking-tight focus:ring-0 focus:border-onyx-950 dark:focus:border-white transition-all">
        </div>

        <div class="relative" x-data="{ open: false, selectedName: 'All Categories' }">
            <button @click="open = !open" @click.away="open = false" type="button" 
                class="w-full flex items-center justify-between pl-8 pr-8 py-5 zen-glass rounded-[1.5rem] text-xs font-black uppercase tracking-widest text-onyx-400">
                <div class="flex items-center gap-4">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                    <span x-text="selectedName"></span>
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="open" x-transition x-cloak class="absolute z-50 mt-4 w-full zen-glass rounded-[1.5rem] shadow-2xl overflow-hidden py-3">
                <div @click="filterCategory = ''; selectedName = 'All Categories'; open = false" class="px-8 py-4 text-[10px] font-black uppercase tracking-widest cursor-pointer hover:bg-black/5 dark:hover:bg-white/5 transition-all">All Categories</div>
                @foreach($items->pluck('category.name')->unique()->filter() as $name)
                <div @click="filterCategory = '{{ $name }}'; selectedName = '{{ $name }}'; open = false" class="px-8 py-4 text-[10px] font-black uppercase tracking-widest cursor-pointer hover:bg-black/5 dark:hover:bg-white/5 transition-all">{{ $name }}</div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Stock Table -->
    <div class="zen-glass squircle overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[10px] font-black text-onyx-400 uppercase tracking-[0.3em] border-b border-black/5 dark:border-white/5 italic">
                        <th class="px-10 py-6">
                            <input type="checkbox" @click="toggleSelectAll()" :checked="selectedItems.length === items.length && items.length > 0" 
                                class="w-5 h-5 rounded-lg border-black/10 dark:border-white/10 text-onyx-950 dark:text-white focus:ring-0 bg-black/5 dark:bg-white/5 cursor-pointer transition-all">
                        </th>
                        <th class="px-10 py-6">Identity</th>
                        <th class="px-10 py-6">Category</th>
                        <th class="px-10 py-6 text-center">Valuation</th>
                        <th class="px-10 py-6 text-center">Status</th>
                        <th class="px-10 py-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5 dark:divide-white/5">
                    <template x-for="item in items" :key="item.id">
                        <tr x-show="(search === '' || item.name.toLowerCase().includes(search.toLowerCase()) || item.code.toLowerCase().includes(search.toLowerCase())) && (filterCategory === '' || item.category === filterCategory)"
                            class="hover:bg-black/5 dark:hover:bg-white/5 transition-all duration-500 group row-action-trigger"
                            :class="selectedItems.includes(item.id) ? 'bg-black/5 dark:bg-white/5' : ''">
                            
                            <td class="px-10 py-8">
                                <div class="transition-opacity duration-300" :class="selectedItems.includes(item.id) ? 'opacity-100' : 'opacity-20 group-hover:opacity-100'">
                                    <input type="checkbox" @click="toggleItem(item.id)" :checked="selectedItems.includes(item.id)" 
                                        class="w-5 h-5 rounded-lg border-black/10 dark:border-white/10 text-onyx-950 dark:text-white focus:ring-0 bg-black/5 dark:bg-white/5 cursor-pointer transition-all">
                                </div>
                            </td>

                            <td class="px-10 py-8">
                                <div class="flex items-center gap-6">
                                    <div class="w-12 h-12 rounded-2xl bg-onyx-950 dark:bg-white text-white dark:text-black flex items-center justify-center font-black text-xs shadow-xl group-hover:scale-110 transition-transform">
                                        <span x-text="item.name.substring(0, 1)"></span>
                                    </div>
                                    <div class="flex flex-col">
                                        <a :href="item.showUrl" class="text-sm font-black uppercase tracking-tight text-onyx-950 dark:text-white" x-text="item.name"></a>
                                        <span class="text-[9px] font-black text-onyx-400 uppercase tracking-[0.2em] mt-1" x-text="item.code"></span>
                                    </div>
                                </div>
                            </td>

                            <td class="px-10 py-8">
                                <span class="px-4 py-2 bg-black/5 dark:bg-white/5 text-[9px] font-black uppercase tracking-widest rounded-xl text-onyx-400" x-text="item.category"></span>
                            </td>

                            <td class="px-10 py-8 text-center">
                                <p class="text-xs font-black italic">Rp <span x-text="new Intl.NumberFormat('id-ID').format(item.price)"></span></p>
                            </td>

                            <td class="px-10 py-8">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-24 bg-black/5 dark:bg-white/5 h-1.5 rounded-full overflow-hidden">
                                        <div class="h-full transition-all duration-1000 ease-out" 
                                            :class="item.stock <= 10 ? 'bg-rose-500 shadow-[0_0_10px_rgba(244,63,94,0.5)]' : 'bg-onyx-950 dark:bg-white'" 
                                            :style="'width: ' + Math.min(100, (item.stock / 50) * 100) + '%'"></div>
                                    </div>
                                    <span class="text-xs font-black italic" x-text="item.stock + ' ' + item.unit"></span>
                                </div>
                            </td>

                            <td class="px-10 py-8 text-right">
                                <div class="flex items-center justify-end gap-2 actions">
                                    <a :href="item.editUrl" class="w-10 h-10 flex items-center justify-center rounded-xl bg-onyx-950/5 dark:bg-white/5 text-onyx-950 dark:text-white hover:bg-onyx-950 dark:hover:bg-white hover:text-white dark:hover:text-black transition-all group/btn">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </a>
                                    <form :action="item.deleteUrl" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Archive this asset?')" 
                                            class="w-10 h-10 flex items-center justify-center rounded-xl bg-rose-500/5 text-rose-500 hover:bg-rose-500 hover:text-white transition-all">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Floating Multi-Action Bar -->
    <div x-show="selectedItems.length > 0" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-20"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-20"
         class="fixed bottom-12 left-1/2 -translate-x-1/2 z-[100] px-8 py-6 rounded-[2.5rem] bg-onyx-950 dark:bg-white text-white dark:text-black shadow-[0_40px_100px_rgba(0,0,0,0.5)] flex items-center gap-10 border border-white/10" x-cloak>
        
        <div class="flex items-center gap-6 pr-10 border-r border-white/10 dark:border-black/10">
            <div class="w-12 h-12 rounded-2xl bg-white/10 dark:bg-black/10 flex items-center justify-center font-black text-xl italic" x-text="selectedItems.length"></div>
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest leading-none">Asets Selected</p>
                <p class="text-[8px] font-black text-onyx-400 uppercase tracking-[0.2em] mt-1">Batch Operations Active</p>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <button class="flex items-center gap-3 px-6 py-4 rounded-2xl hover:bg-white/5 dark:hover:bg-black/5 transition-all group">
                <i data-lucide="printer" class="w-4 h-4 group-hover:scale-110 transition-transform"></i>
                <span class="text-[10px] font-black uppercase tracking-widest">Labels</span>
            </button>
            <button class="flex items-center gap-3 px-6 py-4 rounded-2xl hover:bg-white/5 dark:hover:bg-black/5 transition-all group">
                <i data-lucide="refresh-cw" class="w-4 h-4 group-hover:rotate-180 transition-transform duration-700"></i>
                <span class="text-[10px] font-black uppercase tracking-widest">Sync</span>
            </button>
            <button class="flex items-center gap-3 px-6 py-4 rounded-2xl bg-rose-500/20 text-rose-500 hover:bg-rose-500 transition-all hover:text-white group">
                <i data-lucide="archive" class="w-4 h-4"></i>
                <span class="text-[10px] font-black uppercase tracking-widest">Archive</span>
            </button>
        </div>

        <button @click="selectedItems = []; playFeedback()" class="w-12 h-12 rounded-2xl hover:bg-white/10 dark:hover:bg-black/10 flex items-center justify-center transition-all ml-4">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>
    </div>
</div>
@endsection