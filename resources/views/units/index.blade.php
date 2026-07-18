@extends('layouts')

@section('page_title', 'Units')

@section('breadcrumb')
    <span class="text-onyx-400">INVENTORY</span>
    <i data-lucide="chevron-right" class="w-3 h-3 text-onyx-300"></i>
    <span class="text-onyx-950 dark:text-white">UNITS</span>
@endsection

@section('content')
<div class="space-y-10 animate-in fade-in slide-in-from-bottom-4 duration-700">
    
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="text-5xl lg:text-7xl font-black tracking-tighter italic leading-none uppercase">Unit System</h1>
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-onyx-400 mt-2">Measurement Protocols & Scaling Standards</p>
        </div>
        <a href="{{ route('units.create') }}" class="inline-flex items-center gap-3 bg-onyx-950 dark:bg-white text-white dark:text-black px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] italic hover:scale-105 transition-all shadow-2xl">
            <i data-lucide="plus" class="w-4 h-4"></i>
            New Unit
        </a>
    </div>

    <!-- Data Engine -->
    <div class="zen-glass squircle overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[10px] font-black text-onyx-400 uppercase tracking-[0.3em] border-b border-black/5 dark:border-white/5 italic">
                        <th class="px-10 py-6">Protocol Name</th>
                        <th class="px-10 py-6 text-center">Symbol</th>
                        <th class="px-10 py-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5 dark:divide-white/5">
                    @foreach ($units as $unit)
                    <tr class="hover:bg-black/5 dark:hover:bg-white/5 transition-all duration-500 group">
                        <td class="px-10 py-8">
                            <span class="text-sm font-black uppercase tracking-tight text-onyx-950 dark:text-white">{{ $unit->name }}</span>
                        </td>
                        <td class="px-10 py-8 text-center">
                            <span class="px-4 py-2 bg-black/5 dark:bg-white/5 text-[10px] font-mono font-black text-indigo-500 rounded-xl border border-indigo-500/10">
                                {{ $unit->symbol }}
                            </span>
                        </td>
                        <td class="px-10 py-8 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('units.edit', $unit->id) }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-onyx-950/5 dark:bg-white/5 text-onyx-400 hover:text-onyx-950 dark:hover:text-white transition-all">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </a>
                                <form action="{{ route('units.destroy', $unit->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Archive this unit?')" class="w-10 h-10 flex items-center justify-center rounded-xl bg-rose-500/5 text-rose-500 hover:bg-rose-500 hover:text-white transition-all">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @if($units->isEmpty())
                    <tr>
                        <td colspan="3" class="px-10 py-20 text-center">
                            <p class="text-[10px] font-black uppercase tracking-[0.4em] text-onyx-300">Null Units Detected</p>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
