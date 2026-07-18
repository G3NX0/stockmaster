@extends('layouts')

@section('page_title', 'Profile')

@section('breadcrumb')
    <span class="text-onyx-400">SETTINGS</span>
    <i data-lucide="chevron-right" class="w-3 h-3 text-onyx-300"></i>
    <span class="text-onyx-950 dark:text-white">PROFILE</span>
@endsection

@section('content')
<div class="max-w-4xl space-y-10 animate-in fade-in slide-in-from-bottom-4 duration-700">
    
    <!-- Profile Management Sections -->
    <div class="space-y-10">
        <!-- Update Info -->
        <div class="zen-glass squircle p-10">
            <div class="max-w-xl">
                <h3 class="text-2xl font-black italic tracking-tighter uppercase mb-8">Personnel Identity</h3>
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        {{-- <!-- Update Password -->
        <div class="zen-glass squircle p-10">
            <div class="max-w-xl">
                <h3 class="text-2xl font-black italic tracking-tighter uppercase mb-8">Security Protocol</h3>
                @include('profile.partials.update-password-form')
            </div>
        </div> --}}

        <!-- Delete Account -->
        <div class="p-10 bg-rose-500/5 border border-rose-500/10 rounded-[2.5rem]">
            <div class="max-w-xl">
                <h3 class="text-2xl font-black italic tracking-tighter uppercase mb-8 text-rose-500">Decommission Account</h3>
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>

</div>
@endsection
