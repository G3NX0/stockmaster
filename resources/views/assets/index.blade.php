@extends('layouts')

@section('page_title', 'Assets')

@section('breadcrumb')
    <span class="text-onyx-400">INTELLIGENCE</span>
    <i data-lucide="chevron-right" class="w-3 h-3 text-onyx-300"></i>
    <span class="text-onyx-950 dark:text-white">ASSETS</span>
@endsection

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Company Asset Tracker</h1>
    <p class="mt-1 text-sm text-slate-500">Manage non-inventory items and track their book value depreciation over time.</p>
</div>

<div class="glass rounded-[2.5rem] border border-white/50 shadow-xl overflow-hidden">
    @if($assets->isEmpty())
    <div class="p-20 text-center">
        <div class="w-24 h-24 bg-slate-100 dark:bg-white/5 rounded-full flex items-center justify-center mx-auto mb-6">
            <i data-lucide="hard-drive" class="w-12 h-12 text-slate-400"></i>
        </div>
        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">No Assets Tracked Yet</h3>
        <p class="text-slate-500 max-w-sm mx-auto mb-8">Go to the inventory list and mark items as "Assets" to track their depreciation here.</p>
        <a href="{{ route('items.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-indigo-600 text-white font-bold shadow-lg shadow-indigo-600/20 hover:bg-indigo-700 transition-all">
            Browse Inventory
            <i data-lucide="arrow-right" class="w-4 h-4"></i>
        </a>
    </div>
    @else
    <table class="w-full border-collapse">
        <thead>
            <tr class="bg-slate-50/50 dark:bg-white/5">
                <th class="px-8 py-5 text-left text-[10px] font-black uppercase tracking-widest text-slate-400">Asset Detail</th>
                <th class="px-8 py-5 text-left text-[10px] font-black uppercase tracking-widest text-slate-400">Purchase Info</th>
                <th class="px-8 py-5 text-left text-[10px] font-black uppercase tracking-widest text-slate-400">Asset Life</th>
                <th class="px-8 py-5 text-left text-[10px] font-black uppercase tracking-widest text-slate-400">Book Value</th>
                <th class="px-8 py-5 text-right text-[10px] font-black uppercase tracking-widest text-slate-400">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-white/5">
            @foreach($assets as $asset)
            <tr class="hover:bg-slate-50/30 dark:hover:bg-white/5 transition-colors">
                <td class="px-8 py-6">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-indigo-500/10 rounded-2xl">
                            <i data-lucide="monitor" class="w-5 h-5 text-indigo-600"></i>
                        </div>
                        <div>
                            <span class="block font-bold text-slate-900 dark:text-white leading-tight">{{ $asset->nama_barang }}</span>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">{{ $asset->category->name }} • {{ $asset->kode_barang }}</span>
                        </div>
                    </div>
                </td>
                <td class="px-8 py-6">
                    <span class="block text-sm font-bold text-slate-700 dark:text-slate-300">Rp {{ number_format($asset->harga_barang, 0, ',', '.') }}</span>
                    <span class="text-[10px] text-slate-400 uppercase font-black">{{ $asset->purchase_date ? $asset->purchase_date->format('d M Y') : 'No Date Set' }}</span>
                </td>
                <td class="px-8 py-6">
                    @if($asset->useful_life_months)
                    @php
                        $monthsPassed = $asset->purchase_date ? $asset->purchase_date->diffInMonths(now()) : 0;
                        $percentUsed = min(100, ($monthsPassed / $asset->useful_life_months) * 100);
                    @endphp
                    <div class="w-32">
                        <div class="flex justify-between text-[8px] font-black text-slate-400 uppercase mb-1">
                            <span>Used</span>
                            <span>{{ round($percentUsed) }}%</span>
                        </div>
                        <div class="h-1.5 w-full bg-slate-100 dark:bg-white/5 rounded-full overflow-hidden">
                            <div class="h-full bg-indigo-600" style="width: {{ $percentUsed }}%"></div>
                        </div>
                    </div>
                    @else
                    <span class="text-xs text-slate-400 italic">Not set</span>
                    @endif
                </td>
                <td class="px-8 py-6">
                    <span class="block text-sm font-black text-emerald-600">Rp {{ number_format($asset->current_value, 0, ',', '.') }}</span>
                    <span class="text-[8px] text-slate-400 font-bold uppercase tracking-tighter">Current Depreciated Value</span>
                </td>
                <td class="px-8 py-6 text-right">
                    <div class="flex justify-end items-center gap-2">
                        <a href="{{ route('items.show', $asset->id) }}" class="p-2.5 rounded-xl bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-400 hover:bg-indigo-600 hover:text-white transition-all">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </a>
                        <form action="{{ route('assets.toggle', $asset->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="p-2.5 rounded-xl bg-slate-100 dark:bg-white/5 text-rose-500 hover:bg-rose-500 hover:text-white transition-all" title="Unmark as Asset">
                                <i data-lucide="box" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endsection
