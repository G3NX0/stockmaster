@extends('layouts')

@section('page_title', 'User Management')

@section('breadcrumb')
    <span class="text-onyx-400">SETTINGS</span>
    <i data-lucide="chevron-right" class="w-3 h-3 text-onyx-300"></i>
    <span class="text-onyx-950 dark:text-white">USERS</span>
@endsection

@section('content')
<div class="space-y-10 animate-in fade-in slide-in-from-bottom-4 duration-700">
    
    <!-- Hero Info (Optional, keeping it subtle) -->
    <div class="px-2">
        <p class="text-[10px] font-black uppercase tracking-[0.3em] text-onyx-400 italic">Access Control & Personnel Registry</p>
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

    <!-- User Registry Table -->
    <div class="zen-glass squircle overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[10px] font-black text-onyx-400 uppercase tracking-[0.3em] border-b border-black/5 dark:border-white/5 italic">
                        <th class="px-10 py-6">Personnel Identity</th>
                        <th class="px-10 py-6">Access Protocol (Email)</th>
                        <th class="px-10 py-6">Current Authorization</th>
                        <th class="px-10 py-6 text-right">Modify Access</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5 dark:divide-white/5">
                    @foreach($users as $user)
                    <tr class="hover:bg-black/5 dark:hover:bg-white/5 transition-all duration-500 group">
                        <td class="px-10 py-8">
                            <div class="flex items-center gap-6">
                                <div class="w-12 h-12 rounded-2xl bg-onyx-950 dark:bg-white text-white dark:text-black flex items-center justify-center font-black text-lg shadow-xl group-hover:scale-110 transition-transform duration-500">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <span class="text-sm font-black uppercase tracking-tight text-onyx-950 dark:text-white">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-10 py-8">
                            <span class="text-[10px] font-black text-onyx-400 uppercase tracking-widest italic">{{ $user->email }}</span>
                        </td>
                        <td class="px-10 py-8">
                            <span class="px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest italic {{ $user->role === 'admin' ? 'bg-indigo-500/10 text-indigo-500 border border-indigo-500/20' : 'bg-onyx-950 dark:bg-white text-white dark:text-black' }}">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="px-10 py-8 text-right">
                            @if(Auth::id() !== $user->id)
                            <form action="{{ route('users.update', $user->id) }}" method="POST" class="inline-flex">
                                @csrf @method('PATCH')
                                <select name="role" onchange="this.form.submit()" class="zen-glass border-black/5 dark:border-white/5 rounded-xl px-5 py-2 text-[10px] font-black uppercase tracking-widest outline-none focus:ring-0 focus:border-onyx-950 dark:focus:border-white appearance-none transition-all cursor-pointer">
                                    <option value="staff" {{ $user->role === 'staff' ? 'selected' : '' }}>Set as Staff</option>
                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Set as Admin</option>
                                </select>
                            </form>
                            @else
                            <span class="text-[9px] font-black text-onyx-300 dark:text-onyx-700 uppercase tracking-widest italic">Protected Identity</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="px-10 py-6 border-t border-black/5 dark:border-white/5 bg-black/5 dark:bg-white/5">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
