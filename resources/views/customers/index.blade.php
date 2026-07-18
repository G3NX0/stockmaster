@extends('layouts')

@section('page_title', 'Entities')

@section('breadcrumb')
    <span class="text-onyx-400">INVENTORY</span>
    <i data-lucide="chevron-right" class="w-3 h-3 text-onyx-300"></i>
    <span class="text-onyx-950 dark:text-white">ENTITIES</span>
@endsection

@section('content')
<div class="space-y-10 animate-in fade-in slide-in-from-bottom-4 duration-700">
    
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="text-5xl lg:text-7xl font-black tracking-tighter italic leading-none uppercase">Entities</h1>
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-onyx-400 mt-2">Customer Relationship Management Protocol</p>
        </div>
        <button @click="$dispatch('open-modal', 'add-customer')" class="inline-flex items-center gap-3 bg-onyx-950 dark:bg-white text-white dark:text-black px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] italic hover:scale-105 transition-all shadow-2xl">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            Register Entity
        </button>
    </div>

    <!-- Stats Bento -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="zen-glass squircle p-8 group relative overflow-hidden">
            <div class="flex items-center gap-6 relative z-10">
                <div class="w-14 h-14 rounded-2xl bg-indigo-500 text-white flex items-center justify-center shadow-xl group-hover:scale-110 transition-transform">
                    <i data-lucide="users" class="w-6 h-6"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-onyx-400 mb-1 italic">Total Entities</p>
                    <h3 class="text-3xl font-black italic tracking-tighter uppercase">{{ $customers->count() }}</h3>
                </div>
            </div>
            <i data-lucide="users" class="absolute -right-4 -bottom-4 w-32 h-32 opacity-[0.03] dark:opacity-[0.05]"></i>
        </div>
    </div>

    <!-- Entity Table -->
    <div class="zen-glass squircle overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[10px] font-black text-onyx-400 uppercase tracking-[0.3em] border-b border-black/5 dark:border-white/5 italic">
                        <th class="px-10 py-6">Entity Identity</th>
                        <th class="px-10 py-6">Classification</th>
                        <th class="px-10 py-6">Communication</th>
                        <th class="px-10 py-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5 dark:divide-white/5">
                    @foreach($customers as $customer)
                    <tr class="hover:bg-black/5 dark:hover:bg-white/5 transition-all duration-500 group">
                        <td class="px-10 py-8">
                            <div class="font-black text-sm uppercase tracking-tight text-onyx-950 dark:text-white">{{ $customer->name }}</div>
                            <div class="text-[9px] text-onyx-400 font-black uppercase tracking-widest mt-1 italic">{{ $customer->address ?: 'NO HUB REGISTERED' }}</div>
                        </td>
                        <td class="px-10 py-8">
                            <span class="inline-flex items-center px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest italic bg-indigo-500/10 text-indigo-500 border border-indigo-500/20">
                                {{ $customer->category->name }}
                            </span>
                        </td>
                        <td class="px-10 py-8">
                            <div class="space-y-1">
                                <div class="text-[10px] font-black uppercase tracking-widest text-onyx-400">{{ $customer->email }}</div>
                                <div class="text-[9px] font-black uppercase tracking-[0.2em] text-onyx-300 dark:text-onyx-700 italic">{{ $customer->phone }}</div>
                            </div>
                        </td>
                        <td class="px-10 py-8 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-onyx-950 dark:hover:bg-white hover:text-white dark:hover:text-black text-onyx-400 transition-all">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Entity Modal -->
    <x-modal name="add-customer" focusable>
        <div class="p-10 space-y-10">
            <div>
                <h3 class="text-4xl font-black italic tracking-tighter uppercase">Entity Registration</h3>
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-onyx-400 mt-2">Protocol: CRM Client Enrollment</p>
            </div>

            <form action="{{ route('customers.store') }}" method="POST" class="space-y-8">
                @csrf
                <div class="space-y-4">
                    <label class="text-[10px] font-black text-onyx-400 uppercase tracking-[0.4em] ml-2">Identity Name</label>
                    <x-text-input type="text" name="name" required placeholder="ENTITY_NAME_01" />
                </div>

                <div class="space-y-4">
                    <label class="text-[10px] font-black text-onyx-400 uppercase tracking-[0.4em] ml-2">Classification Tier</label>
                    <select name="customer_category_id" required class="w-full zen-glass border-black/5 dark:border-white/5 rounded-2xl px-8 py-5 text-sm font-black uppercase tracking-tight focus:ring-0 focus:border-onyx-950 dark:focus:border-white outline-none appearance-none transition-all">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" class="bg-onyx-900 text-white font-black">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-onyx-400 uppercase tracking-[0.4em] ml-2">Communication (Email)</label>
                        <x-text-input type="email" name="email" placeholder="CONTACT@ENTITY.COM" />
                    </div>
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-onyx-400 uppercase tracking-[0.4em] ml-2">Protocol (Phone)</label>
                        <x-text-input type="text" name="phone" placeholder="+62 800-0000-0000" />
                    </div>
                </div>

                <div class="flex items-center justify-end pt-4 gap-4">
                    <x-secondary-button @click="$dispatch('close')">Decline</x-secondary-button>
                    <x-primary-button>Protocolize Entity</x-primary-button>
                </div>
            </form>
        </div>
    </x-modal>
</div>
@endsection
