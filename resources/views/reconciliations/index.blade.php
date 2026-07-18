@extends('layouts')

@section('page_title', 'Reconciliations')

@section('breadcrumb')
    <span class="text-onyx-400">SYSTEM</span>
    <i data-lucide="chevron-right" class="w-3 h-3 text-onyx-300"></i>
    <span class="text-onyx-950 dark:text-white">RECONCILIATION</span>
@endsection

@section('content')
<div class="mb-8 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Stock Reconciliation</h1>
        <p class="mt-1 text-sm text-slate-500">Audit stok fisik vs sistem untuk akurasi inventaris.</p>
    </div>
    <a href="{{ route('reconciliations.create') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-2xl font-bold shadow-lg shadow-indigo-600/20 hover:bg-indigo-700 transition-all flex items-center gap-2">
        <i data-lucide="plus" class="w-5 h-5"></i>
        Mulai Audit Baru
    </a>
</div>

@if(session('success'))
<div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 rounded-2xl text-sm font-bold flex items-center gap-3">
    <i data-lucide="check-circle" class="w-5 h-5"></i>
    {{ session('success') }}
</div>
@endif

<div class="glass border border-white/50 rounded-3xl shadow-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-100/50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                    <th class="px-8 py-5 text-sm font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Tanggal</th>
                    <th class="px-8 py-5 text-sm font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Barang</th>
                    <th class="px-8 py-5 text-sm font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">System</th>
                    <th class="px-8 py-5 text-sm font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Fisik</th>
                    <th class="px-8 py-5 text-sm font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider text-right">Selisih</th>
                    <th class="px-8 py-5 text-sm font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Audit Oleh</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @foreach($reconciliations as $rec)
                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors group">
                    <td class="px-8 py-6 text-sm text-slate-600 dark:text-slate-400">
                        {{ $rec->created_at->format('d M Y, H:i') }}
                    </td>
                    <td class="px-8 py-6">
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-900 dark:text-white">{{ $rec->item->nama_barang }}</span>
                            <span class="text-[10px] text-slate-400 font-mono">{{ $rec->item->kode_barang }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-6 text-sm text-slate-600 dark:text-slate-400">{{ $rec->system_stock }}</td>
                    <td class="px-8 py-6 text-sm font-bold text-slate-900 dark:text-white">{{ $rec->physical_stock }}</td>
                    <td class="px-8 py-6 text-right">
                        <span class="px-3 py-1 rounded-lg text-xs font-black {{ $rec->difference >= 0 ? 'bg-emerald-500/10 text-emerald-600' : 'bg-rose-500/10 text-rose-600' }}">
                            {{ $rec->difference > 0 ? '+' : '' }}{{ $rec->difference }}
                        </span>
                    </td>
                    <td class="px-8 py-6 text-sm text-slate-600 dark:text-slate-400">
                        {{ $rec->user->name }}
                    </td>
                </tr>
                @endforeach
                @if($reconciliations->isEmpty())
                <tr>
                    <td colspan="6" class="px-8 py-12 text-center text-slate-400 italic font-medium">Belum ada riwayat rekonsiliasi stok.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    <div class="px-8 py-4 border-t border-slate-100 dark:border-slate-800">
        {{ $reconciliations->links() }}
    </div>
</div>
@endsection
