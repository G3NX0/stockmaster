@extends('layouts')

@section('page_title', 'Activity Logs')

@section('breadcrumb')
    <span class="text-onyx-400">SYSTEM</span>
    <i data-lucide="chevron-right" class="w-3 h-3 text-onyx-300"></i>
    <span class="text-onyx-950 dark:text-white">LOGS</span>
@endsection

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold tracking-tight text-slate-900 text-slate-900 dark:text-white">Log Aktivitas Sistem</h1>
    <p class="mt-1 text-sm text-slate-500">Pantau semua perubahan data yang dilakukan oleh tim Anda.</p>
</div>

<div class="glass border border-white/50 rounded-3xl shadow-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-100/50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                    <th class="px-8 py-5 text-sm font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Waktu</th>
                    <th class="px-8 py-5 text-sm font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">User</th>
                    <th class="px-8 py-5 text-sm font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Aksi</th>
                    <th class="px-8 py-5 text-sm font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Detail</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @foreach($logs as $log)
                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors group">
                    <td class="px-8 py-6 text-sm text-slate-600 dark:text-slate-400">
                        {{ $log->created_at->format('d M Y, H:i') }}
                    </td>
                    <td class="px-8 py-6 text-sm font-bold text-slate-900 dark:text-slate-200">
                        {{ $log->causer->name ?? 'System' }}
                    </td>
                    <td class="px-8 py-6">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold capitalize
                            {{ $log->description === 'created' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $log->description === 'updated' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $log->description === 'deleted' ? 'bg-red-100 text-red-700' : '' }}
                        ">
                            {{ $log->description }}
                        </span>
                    </td>
                    <td class="px-8 py-6 text-sm text-slate-500 dark:text-slate-400">
                        {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                    </td>
                </tr>
                @endforeach
                @if($logs->isEmpty())
                <tr>
                    <td colspan="4" class="px-8 py-12 text-center text-slate-400 italic">Belum ada aktivitas tercatat.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    <div class="px-8 py-4 border-t border-slate-100 dark:border-slate-800">
        {{ $logs->links() }}
    </div>
</div>
@endsection
