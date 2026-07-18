@extends('layouts')

@section('page_title', 'Procurement')

@section('breadcrumb')
    <span class="text-onyx-400">WORKFLOW</span>
    <i data-lucide="chevron-right" class="w-3 h-3 text-onyx-300"></i>
    <span class="text-onyx-950 dark:text-white">PROCUREMENT</span>
@endsection

@section('content')
<div x-data="{ view: 'grid' }" class="space-y-10 animate-in fade-in slide-in-from-bottom-4 duration-700">
    
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="text-5xl lg:text-7xl font-black tracking-tighter italic leading-none uppercase">Procurement</h1>
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-onyx-400 mt-2">Automated Supply Chain & Purchase Protocol</p>
        </div>
        <div class="flex items-center gap-4">
            <!-- View Toggle -->
            <div class="flex p-1 rounded-2xl bg-black/5 dark:bg-white/5 border border-black/5 dark:border-white/5">
                <button @click="view = 'grid'; playFeedback()" :class="view === 'grid' ? 'bg-white dark:bg-onyx-800 shadow-lg' : 'text-onyx-400'" class="px-6 py-3 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all">
                    Grid
                </button>
                <button @click="view = 'board'; playFeedback()" :class="view === 'board' ? 'bg-white dark:bg-onyx-800 shadow-lg' : 'text-onyx-400'" class="px-6 py-3 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all">
                    Board
                </button>
            </div>
            <form action="{{ route('pos.generate') }}" method="GET">
                <button type="submit" class="inline-flex items-center gap-3 bg-onyx-950 dark:bg-white text-white dark:text-black px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] italic hover:scale-105 transition-all shadow-2xl">
                    <i data-lucide="sparkles" class="w-4 h-4 text-indigo-500"></i>
                    Generate Flux
                </button>
            </form>
        </div>
    </div>

    <!-- Grid View -->
    <div x-show="view === 'grid'" x-transition class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($pos as $po)
        <div class="zen-glass squircle p-8 group hover:scale-[1.02] transition-all duration-500 flex flex-col gap-8 relative overflow-hidden">
            <div class="flex justify-between items-start relative z-10">
                <div class="w-14 h-14 rounded-2xl bg-onyx-950 dark:bg-white text-white dark:text-black flex items-center justify-center shadow-xl group-hover:scale-110 transition-transform duration-500">
                    <i data-lucide="shopping-cart" class="w-6 h-6"></i>
                </div>
                <span class="px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest italic {{ $po->status === 'draft' ? 'bg-amber-500/10 text-amber-500 border border-amber-500/20' : 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20' }}">
                    {{ $po->status }}
                </span>
            </div>
            
            <div class="relative z-10">
                <h3 class="text-2xl font-black tracking-tighter italic uppercase text-onyx-950 dark:text-white">{{ $po->po_number }}</h3>
                <p class="text-[10px] font-black text-onyx-400 uppercase tracking-widest mt-1">{{ $po->supplier->name }}</p>
            </div>

            <div class="space-y-4 pt-4 border-t border-black/5 dark:border-white/5 relative z-10">
                <div class="flex justify-between items-center">
                    <span class="text-[9px] font-black text-onyx-400 uppercase tracking-widest">Valuation</span>
                    <span class="text-lg font-black italic text-indigo-500">Rp {{ number_format($po->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>

            <a href="{{ route('pos.show', $po->id) }}" class="mt-4 w-full py-5 rounded-2xl bg-black/5 dark:bg-white/5 flex items-center justify-center gap-3 text-[10px] font-black uppercase tracking-widest hover:bg-onyx-950 dark:hover:bg-white hover:text-white dark:hover:text-black transition-all group/btn">
                View Intelligence
                <i data-lucide="arrow-right" class="w-4 h-4 group-hover/btn:translate-x-2 transition-transform"></i>
            </a>
        </div>
        @endforeach
    </div>

    <!-- Board View (Kanban) -->
    <div x-show="view === 'board'" x-transition class="flex gap-8 overflow-x-auto custom-scrollbar pb-10">
        @foreach(['draft', 'ordered', 'received', 'cancelled'] as $status)
        <div class="flex-shrink-0 w-96 flex flex-col gap-6">
            <div class="flex items-center justify-between px-6">
                <h3 class="text-[10px] font-black uppercase tracking-[0.6em] text-onyx-400 italic">{{ $status }}</h3>
                <span class="w-6 h-6 rounded-lg bg-black/5 dark:bg-white/5 flex items-center justify-center text-[10px] font-black">
                    {{ $pos->where('status', $status)->count() }}
                </span>
            </div>
            
            <div class="kanban-column flex flex-col gap-6 min-h-[500px] p-2 rounded-[2rem] bg-black/[0.02] dark:bg-white/[0.02] border border-dashed border-black/5 dark:border-white/5" data-status="{{ $status }}">
                @foreach($pos->where('status', $status) as $po)
                <div class="zen-glass rounded-3xl p-6 space-y-4 cursor-grab active:cursor-grabbing hover:scale-[1.02] transition-all" data-id="{{ $po->id }}">
                    <div class="flex justify-between items-center">
                        <span class="text-[9px] font-black text-onyx-400 uppercase tracking-widest">{{ $po->po_number }}</span>
                        <i data-lucide="grip-vertical" class="w-4 h-4 text-onyx-300"></i>
                    </div>
                    <h4 class="text-sm font-black uppercase tracking-tight">{{ $po->supplier->name }}</h4>
                    <div class="flex justify-between items-end pt-4 border-t border-black/5">
                        <span class="text-[9px] font-black text-onyx-400 uppercase tracking-widest italic">VALUATION</span>
                        <span class="text-sm font-black italic">Rp {{ number_format($po->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

    @if($pos->isEmpty())
    <div class="zen-glass squircle p-32 flex flex-col items-center text-center gap-8 border-dashed border-2 border-black/5 dark:border-white/5">
        <div class="w-24 h-24 rounded-full bg-onyx-950 dark:bg-white text-white dark:text-black flex items-center justify-center shadow-2xl">
            <i data-lucide="shopping-bag" class="w-10 h-10"></i>
        </div>
        <h2 class="text-4xl font-black italic tracking-tighter uppercase">No Active Protocols</h2>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const columns = document.querySelectorAll('.kanban-column');
        columns.forEach(column => {
            new Sortable(column, {
                group: 'pos',
                animation: 250,
                ghostClass: 'opacity-20',
                dragClass: 'scale-105',
                onEnd: async (evt) => {
                    const poId = evt.item.dataset.id;
                    const newStatus = evt.to.dataset.status;
                    
                    if (evt.from !== evt.to) {
                        try {
                            const response = await fetch(`/pos/${poId}/status`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ status: newStatus })
                            });
                            
                            if (response.ok) {
                                window.dispatchEvent(new CustomEvent('show-toast', {
                                    detail: { message: `PROTOCOL ${poId} UPDATED TO ${newStatus.toUpperCase()}`, type: 'success' }
                                }));
                            }
                        } catch (error) {
                            console.error('Flux Update Failed:', error);
                        }
                    }
                }
            });
        });
    });
</script>
@endpush
