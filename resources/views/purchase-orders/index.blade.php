@extends('layouts')

@section('page_title', 'Purchase Orders')

@section('breadcrumb')
    <span class="text-onyx-400">WORKFLOW</span>
    <i data-lucide="chevron-right" class="w-3 h-3 text-onyx-300"></i>
    <span class="text-onyx-950 dark:text-white">PURCHASE ORDERS</span>
@endsection

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Smart Purchase Orders</h1>
    <p class="mt-1 text-sm text-slate-500">Otomatisasi pengadaan barang untuk item yang stoknya menipis.</p>
</div>

<div class="space-y-8">
    @forelse($lowStockItems as $supplierId => $items)
    @php $supplier = $items->first()->supplier; @endphp
    <div class="glass border border-white/50 rounded-[2.5rem] p-8 shadow-xl">
        <div class="flex justify-between items-start mb-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-indigo-600/10 text-indigo-600 flex items-center justify-center">
                    <i data-lucide="truck" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">{{ $supplier->name ?? 'No Supplier Assigned' }}</h3>
                    <p class="text-xs text-slate-500">{{ $items->count() }} items need restocking</p>
                    @if(!$supplier)
                    <span class="inline-block mt-2 px-3 py-1 bg-rose-500/10 text-rose-600 rounded-lg text-[9px] font-black uppercase tracking-widest border border-rose-500/20">
                        Assign supplier to generate PO
                    </span>
                    @endif
                </div>
            </div>
            <form action="{{ route('purchase-orders.generate') }}" method="POST">
                @csrf
                <input type="hidden" name="supplier_id" value="{{ $supplierId }}">
                @foreach($items as $index => $item)
                    <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                    <input type="hidden" name="items[{{ $index }}][quantity]" value="{{ $item->min_stock * 2 }}">
                @endforeach
                <button type="submit" {{ !$supplier ? 'disabled' : '' }} class="px-6 py-3 {{ !$supplier ? 'bg-slate-200 text-slate-400 cursor-not-allowed' : 'bg-indigo-600 text-white hover:bg-indigo-700 shadow-lg shadow-indigo-600/20' }} rounded-xl font-black text-xs uppercase tracking-widest transition-all flex items-center gap-2">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                    Generate PO PDF
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-white/5">
                        <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Item</th>
                        <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Current Stock</th>
                        <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Min. Stock</th>
                        <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Suggested Order</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-white/5">
                    @foreach($items as $item)
                    <tr>
                        <td class="px-4 py-5">
                            <div class="flex flex-col">
                                <span class="font-bold text-slate-900 dark:text-white">{{ $item->nama_barang }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $item->kode_barang }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-5">
                            <span class="px-2 py-1 bg-rose-500/10 text-rose-600 rounded-lg font-black text-xs">
                                {{ $item->stok_barang }} {{ $item->unit->name }}
                            </span>
                        </td>
                        <td class="px-4 py-5 text-sm text-slate-500">
                            {{ $item->min_stock }} {{ $item->unit->name }}
                        </td>
                        <td class="px-4 py-5 text-right font-black text-indigo-600">
                            {{ $item->min_stock * 2 }} {{ $item->unit->name }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @empty
    <div class="glass border border-white/50 rounded-[2.5rem] p-12 text-center">
        <div class="w-20 h-20 bg-emerald-500/10 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6">
            <i data-lucide="check-circle-2" class="w-10 h-10"></i>
        </div>
        <h3 class="text-xl font-bold text-slate-900 dark:text-white">All Stock Levels Normal</h3>
        <p class="text-sm text-slate-500 mt-2">Tidak ada barang yang memerlukan pengadaan saat ini.</p>
    </div>
    @endforelse
</div>
@endsection
