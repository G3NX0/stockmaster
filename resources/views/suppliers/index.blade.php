@extends('layouts')

@section('page_title', 'Vendors')

@section('breadcrumb')
    <span class="text-onyx-400">INVENTORY</span>
    <i data-lucide="chevron-right" class="w-3 h-3 text-onyx-300"></i>
    <span class="text-onyx-950 dark:text-white">VENDORS</span>
@endsection

@section('content')
<div class="space-y-10 animate-in fade-in slide-in-from-bottom-4 duration-700">
    
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="text-5xl lg:text-7xl font-black tracking-tighter italic leading-none uppercase">Vendors</h1>
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-onyx-400 mt-2">Enterprise Supplier & Resource Ecosystem</p>
        </div>
        <a href="{{ route('suppliers.create') }}" class="inline-flex items-center gap-3 bg-onyx-950 dark:bg-white text-white dark:text-black px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] italic hover:scale-105 transition-all shadow-2xl">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Add Vendor
        </a>
    </div>

    <!-- Vendor Table -->
    <div class="zen-glass squircle overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[10px] font-black text-onyx-400 uppercase tracking-[0.3em] border-b border-black/5 dark:border-white/5 italic">
                        <th class="px-10 py-6">Vendor Identity</th>
                        <th class="px-10 py-6">Communication Protocol</th>
                        <th class="px-10 py-6">Logistic Hub</th>
                        <th class="px-10 py-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5 dark:divide-white/5">
                    @foreach($suppliers as $supplier)
                    <tr class="hover:bg-black/5 dark:hover:bg-white/5 transition-all duration-500 group">
                        <td class="px-10 py-8">
                            <div class="flex items-center gap-6">
                                <div class="w-12 h-12 rounded-2xl bg-onyx-950 dark:bg-white text-white dark:text-black flex items-center justify-center font-black text-lg shadow-xl group-hover:scale-110 transition-transform duration-500">
                                    {{ substr($supplier->name, 0, 1) }}
                                </div>
                                <span class="text-sm font-black uppercase tracking-tight text-onyx-950 dark:text-white">{{ $supplier->name }}</span>
                            </div>
                        </td>
                        <td class="px-10 py-8">
                            <div class="space-y-2">
                                <div class="flex items-center gap-3 text-[10px] font-black uppercase tracking-widest text-onyx-400">
                                    <i data-lucide="mail" class="w-3.5 h-3.5"></i>
                                    {{ $supplier->email ?? 'N/A' }}
                                </div>
                                <div class="flex items-center gap-3 text-[10px] font-black uppercase tracking-widest text-onyx-400">
                                    <i data-lucide="phone" class="w-3.5 h-3.5"></i>
                                    {{ $supplier->phone ?? 'N/A' }}
                                </div>
                            </div>
                        </td>
                        <td class="px-10 py-8">
                            <span class="text-[10px] font-black uppercase tracking-widest text-onyx-400 italic line-clamp-1 max-w-xs">
                                {{ $supplier->address ?? 'NO REGISTERED HUB' }}
                            </span>
                        </td>
                        <td class="px-10 py-8 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('suppliers.edit', $supplier->id) }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-onyx-950/5 dark:bg-white/5 text-onyx-950 dark:text-white hover:bg-onyx-950 dark:hover:bg-white hover:text-white dark:hover:text-black transition-all">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </a>
                                <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" class="inline" onsubmit="return confirm('Decommission this vendor?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-10 h-10 flex items-center justify-center rounded-xl bg-rose-500/5 text-rose-500 hover:bg-rose-500 hover:text-white transition-all">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
