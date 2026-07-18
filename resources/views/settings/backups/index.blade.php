@extends('layouts')

@section('page_title', 'Recovery')

@section('breadcrumb')
    <span class="text-onyx-400">SETTINGS</span>
    <i data-lucide="chevron-right" class="w-3 h-3 text-onyx-300"></i>
    <span class="text-onyx-950 dark:text-white">RECOVERY</span>
@endsection

@section('content')
<div class="space-y-10 animate-in fade-in slide-in-from-bottom-4 duration-700">
    
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="text-5xl lg:text-7xl font-black tracking-tighter italic leading-none uppercase">Recovery</h1>
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-onyx-400 mt-2">Database Backup & Disaster Recovery Protocol</p>
        </div>
        <form action="{{ route('backups.create') }}" method="POST">
            @csrf
            <button type="submit" class="inline-flex items-center gap-3 bg-onyx-950 dark:bg-white text-white dark:text-black px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] italic hover:scale-105 transition-all shadow-2xl">
                <i data-lucide="database" class="w-4 h-4 text-indigo-500"></i>
                Initialize Backup
            </button>
        </form>
    </div>

    <!-- Status Messages -->
    @if(session('success'))
    <div class="p-6 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 rounded-2xl text-[10px] font-black uppercase tracking-widest italic flex items-center gap-4 animate-in slide-in-from-top-4">
        <i data-lucide="check-circle" class="w-5 h-5"></i>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="p-6 bg-rose-500/10 border border-rose-500/20 text-rose-500 rounded-2xl text-[10px] font-black uppercase tracking-widest italic flex items-center gap-4 animate-in slide-in-from-top-4">
        <i data-lucide="alert-circle" class="w-5 h-5"></i>
        {{ session('error') }}
    </div>
    @endif

    <!-- Recovery Table -->
    <div class="zen-glass squircle overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[10px] font-black text-onyx-400 uppercase tracking-[0.3em] border-b border-black/5 dark:border-white/5 italic">
                        <th class="px-10 py-6">Archive Identity</th>
                        <th class="px-10 py-6">Volume</th>
                        <th class="px-10 py-6">Timestamp</th>
                        <th class="px-10 py-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5 dark:divide-white/5">
                    @foreach($backups as $backup)
                    <tr class="hover:bg-black/5 dark:hover:bg-white/5 transition-all duration-500 group">
                        <td class="px-10 py-8">
                            <div class="flex items-center gap-4">
                                <i data-lucide="file-archive" class="w-5 h-5 text-indigo-500"></i>
                                <span class="text-sm font-black uppercase tracking-tight text-onyx-950 dark:text-white">{{ $backup['file_name'] }}</span>
                            </div>
                        </td>
                        <td class="px-10 py-8">
                            <span class="text-[10px] font-black text-onyx-400 uppercase tracking-widest italic">{{ $backup['file_size'] }}</span>
                        </td>
                        <td class="px-10 py-8">
                            <span class="text-[10px] font-black text-onyx-400 uppercase tracking-widest italic">{{ $backup['last_modified'] }}</span>
                        </td>
                        <td class="px-10 py-8 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('backups.download', $backup['file_name']) }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-indigo-500/10 text-indigo-500 hover:bg-indigo-500 hover:text-white transition-all">
                                    <i data-lucide="download" class="w-4 h-4"></i>
                                </a>
                                <form action="{{ route('backups.destroy', $backup['file_name']) }}" method="POST" class="inline-block" onsubmit="return confirm('Decommission this archive?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-10 h-10 flex items-center justify-center rounded-xl bg-rose-500/10 text-rose-500 hover:bg-rose-500 hover:text-white transition-all">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @if(empty($backups))
                    <tr>
                        <td colspan="4" class="px-10 py-20 text-center text-onyx-300 dark:text-onyx-700 italic text-[10px] font-black uppercase tracking-[0.3em]">
                            No Recovery Archives Found.
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- Security Bento -->
    <div class="zen-glass squircle p-10 bg-indigo-500/5 border-indigo-500/10 relative overflow-hidden group">
        <div class="flex flex-col md:flex-row items-center gap-8 relative z-10">
            <div class="w-20 h-20 bg-indigo-500 text-white rounded-[2rem] flex items-center justify-center shadow-2xl group-hover:rotate-12 transition-transform duration-500">
                <i data-lucide="shield-check" class="w-10 h-10"></i>
            </div>
            <div class="flex-1 text-center md:text-left space-y-4">
                <h4 class="text-2xl font-black italic tracking-tighter uppercase">Enterprise Security Protocol</h4>
                <p class="text-onyx-400 text-xs font-medium leading-relaxed max-w-2xl italic">
                    Archives are stored using high-compression ZIP protocols. We strictly recommend periodic off-site downloads to external hardware or secondary cloud clusters to ensure maximum business continuity against local node failures.
                </p>
            </div>
        </div>
        <i data-lucide="shield-check" class="absolute -right-4 -bottom-4 w-40 h-40 opacity-[0.03] dark:opacity-[0.05]"></i>
    </div>
</div>
@endsection
