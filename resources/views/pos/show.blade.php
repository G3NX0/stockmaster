@extends('layouts')

@section('page_title', 'Order Detail')

@section('breadcrumb')
    <span class="text-onyx-600 dark:text-onyx-400">WORKFLOW</span>
    <i data-lucide="chevron-right" class="w-3 h-3 text-onyx-300"></i>
    <a href="{{ route('pos.index') }}" class="text-onyx-600 dark:text-onyx-400 hover:text-onyx-950 dark:hover:text-white transition-colors">SMART ORDERS</a>
    <i data-lucide="chevron-right" class="w-3 h-3 text-onyx-300"></i>
    <span class="text-onyx-950 dark:text-white uppercase">{{ $purchaseOrder->po_number }}</span>
@endsection

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <a href="{{ route('pos.index') }}" class="inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-500 hover:text-indigo-600 transition-colors mb-4">
            <i data-lucide="arrow-left" class="w-3 h-3"></i>
            Back to Procurement
        </a>
        <h1 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white uppercase">{{ $purchaseOrder->po_number }}</h1>
    </div>
    <div class="flex gap-4">
        <form action="{{ route('pos.update-status', ['po' => $purchaseOrder->id]) }}" method="POST" class="relative group">
            @csrf
            <select name="status" onchange="this.form.submit()" 
                    class="bg-indigo-600/20 hover:bg-indigo-600/40 border border-indigo-500/30 rounded-2xl px-6 py-3 text-xs font-black uppercase tracking-widest text-indigo-400 outline-none focus:ring-2 focus:ring-indigo-500 transition-all cursor-pointer appearance-none pr-12">
                <option value="draft" class="bg-slate-900 text-white" {{ $purchaseOrder->status === 'draft' ? 'selected' : '' }}>Mark as Draft</option>
                <option value="sent" class="bg-slate-900 text-white" {{ $purchaseOrder->status === 'sent' ? 'selected' : '' }}>Mark as Sent</option>
                <option value="received" class="bg-slate-900 text-white" {{ $purchaseOrder->status === 'received' ? 'selected' : '' }}>Mark as Received</option>
                <option value="cancelled" class="bg-slate-900 text-white" {{ $purchaseOrder->status === 'cancelled' ? 'selected' : '' }}>Cancel Order</option>
            </select>
            <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-indigo-400">
                <i data-lucide="chevron-down" class="w-4 h-4"></i>
            </div>
        </form>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 space-y-8">
        <div class="glass rounded-[2.5rem] border border-black/5 dark:border-white/20 overflow-hidden">
            <div class="px-8 py-6 bg-black/5 dark:bg-white/5 border-b border-black/5 dark:border-white/10 flex justify-between items-center">
                <h3 class="font-bold text-onyx-950 dark:text-white uppercase tracking-widest text-xs">Ordered Items</h3>
                <span class="text-[10px] font-black text-onyx-400 dark:text-slate-500">{{ count($purchaseOrder->items) }} Products</span>
            </div>
            <table class="w-full text-left">
                <thead>
                    <tr class="text-[10px] font-black uppercase tracking-widest text-onyx-400 dark:text-slate-500 border-b border-black/5 dark:border-white/5">
                        <th class="px-8 py-4">Item Name</th>
                        <th class="px-8 py-4 text-center">Qty</th>
                        <th class="px-8 py-4 text-right">Estimate Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($purchaseOrder->items as $item)
                    <tr>
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-black/5 dark:bg-white/5 flex items-center justify-center text-indigo-500 dark:text-indigo-400 font-bold border border-black/5 dark:border-white/10">
                                    {{ substr($item['name'], 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-bold text-onyx-950 dark:text-white group-hover:text-indigo-500 dark:group-hover:text-indigo-400 transition-colors">{{ $item['name'] }}</div>
                                    <div class="text-[10px] text-onyx-400 dark:text-slate-500 font-black uppercase tracking-widest">Product ID: #{{ $item['id'] }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6 text-center">
                            <span class="px-3 py-1 rounded-lg bg-indigo-500/10 text-indigo-400 font-black text-sm border border-indigo-500/20">
                                {{ $item['qty'] }}
                            </span>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="font-bold text-onyx-950 dark:text-white">Rp {{ number_format($item['qty'] * (\App\Models\Item::find($item['id'])->harga_barang ?? 0), 0, ',', '.') }}</div>
                            <div class="text-[10px] text-onyx-400 dark:text-slate-500 font-medium">Estimated cost</div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="space-y-8">
        <div class="glass p-8 rounded-[2.5rem] border border-black/5 dark:border-white/20">
            <h3 class="font-bold text-onyx-950 dark:text-white uppercase tracking-widest text-xs mb-6">Supplier Info</h3>
            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 bg-indigo-500/10 rounded-2xl flex items-center justify-center text-indigo-600 dark:text-indigo-500">
                    <i data-lucide="building-2" class="w-6 h-6"></i>
                </div>
                <div>
                    <div class="font-bold text-onyx-950 dark:text-white">{{ $purchaseOrder->supplier->name }}</div>
                    <div class="text-[10px] text-onyx-400 dark:text-slate-500 uppercase font-black">Contracted Vendor</div>
                </div>
            </div>
            <div class="space-y-4">
                <div class="p-6 rounded-[2rem] bg-indigo-600/10 border border-indigo-500/20 relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-indigo-500/10 rounded-full blur-2xl group-hover:bg-indigo-500/20 transition-all duration-700"></div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-indigo-600/70 dark:text-indigo-400/70 mb-2">Total Order Value</p>
                    <p class="text-3xl font-black text-onyx-950 dark:text-white tracking-tighter">Rp {{ number_format($purchaseOrder->total_amount, 0, ',', '.') }}</p>
                    <div class="mt-4 flex items-center gap-2 text-[10px] font-bold text-onyx-400 dark:text-slate-500 uppercase tracking-widest">
                        <i data-lucide="info" class="w-3 h-3"></i>
                        Incl. all selected items
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
