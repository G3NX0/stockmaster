@extends('layouts')

@section('page_title', 'Item Detail')

@section('breadcrumb')
    <span class="text-onyx-400">INVENTORY</span>
    <i data-lucide="chevron-right" class="w-3 h-3 text-onyx-300"></i>
    <a href="{{ route('items.index') }}" class="hover:text-onyx-950 dark:hover:text-white transition-colors">ITEMS</a>
    <i data-lucide="chevron-right" class="w-3 h-3 text-onyx-300"></i>
    <span class="text-onyx-950 dark:text-white uppercase">{{ $item->kode_barang }}</span>
@endsection

@section('content')
<div class="mb-8">
    <a href="{{ route('items.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-indigo-600 transition-colors mb-4">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        Kembali ke Daftar
    </a>
    <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Detail Barang: {{ $item->nama_barang }}</h1>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Main Info -->
    <div class="lg:col-span-2 space-y-8">
        <div class="glass border border-white/20 dark:border-white/5 rounded-[3rem] p-10 shadow-2xl relative overflow-hidden">
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-indigo-500/10 rounded-full blur-[100px]"></div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10 relative z-10">
                <div class="space-y-6">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-2">Nama Barang</p>
                        <p class="text-2xl font-black text-slate-900 dark:text-white">{{ $item->nama_barang }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-2">Kode Barang</p>
                        <p class="text-lg font-mono font-black text-indigo-500 uppercase">{{ $item->kode_barang }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-2">Harga Satuan</p>
                        <p class="text-xl font-black text-slate-900 dark:text-white">Rp {{ number_format($item->harga_barang, 0, ',', '.') }}</p>
                    </div>
                </div>
                <div class="space-y-6">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-2">Kategori</p>
                        <span class="inline-flex items-center px-4 py-2 bg-indigo-500/10 text-indigo-500 rounded-xl text-xs font-black border border-indigo-500/20 uppercase tracking-widest">
                            {{ $item->category->name ?? 'Tanpa Kategori' }}
                        </span>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-2">Satuan</p>
                        <p class="text-lg text-slate-700 dark:text-slate-200 font-black uppercase tracking-tight">{{ $item->unit->name ?? '-' }} ({{ $item->unit->symbol ?? '-' }})</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-2">Supplier Utama</p>
                        <p class="text-lg text-slate-700 dark:text-slate-200 font-black uppercase tracking-tight">{{ $item->supplier->name ?? 'Internal / Mandiri' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="glass p-8 rounded-[2.5rem] border border-white/20 dark:border-white/5 shadow-xl relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition-all"></div>
                <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-3">Stok Saat Ini</p>
                <p class="text-4xl font-black {{ $item->stok_barang <= $item->min_stock ? 'text-rose-500' : 'text-slate-900 dark:text-white' }} tracking-tighter">
                    {{ $item->stok_barang }}
                </p>
            </div>
            <div class="glass p-8 rounded-[2.5rem] border border-white/20 dark:border-white/5 shadow-xl relative overflow-hidden group">
                <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-3">Stok Minimum</p>
                <p class="text-4xl font-black text-slate-400 dark:text-slate-600 tracking-tighter">{{ $item->min_stock }}</p>
            </div>
            <div class="glass p-8 rounded-[2.5rem] border border-white/20 dark:border-white/5 shadow-xl relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-indigo-500/10 rounded-full blur-2xl group-hover:bg-indigo-500/20 transition-all"></div>
                <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-3">Estimasi Habis</p>
                <p class="text-3xl font-black text-indigo-500 tracking-tighter">{{ $prediction }} Hari lagi</p>
            </div>
        </div>
    </div>

    <!-- QR Code Section -->
    <div class="space-y-8">
        <div class="glass p-10 rounded-[3rem] border border-white/20 dark:border-white/5 shadow-2xl text-center relative overflow-hidden group">
            <div class="absolute -top-12 -right-12 w-48 h-48 bg-indigo-500/10 rounded-full blur-[80px] group-hover:bg-indigo-500/20 transition-all duration-700"></div>
            <h3 class="text-sm font-black text-slate-900 dark:text-white mb-8 relative z-10 uppercase tracking-[0.3em]">Quick ID QR</h3>
            <div id="qrcode" class="flex justify-center mb-8 p-6 bg-white/40 dark:bg-white/5 backdrop-blur-md rounded-[2.5rem] shadow-inner border border-white/30 dark:border-white/5 relative z-10 transform group-hover:scale-105 transition-transform duration-500"></div>
            <p class="text-[10px] text-slate-500 dark:text-slate-500 font-black uppercase tracking-widest relative z-10">Scan for instant stock verification</p>
            <button onclick="downloadQR()" class="mt-8 w-full py-4 bg-indigo-600/10 text-indigo-500 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-indigo-600 hover:text-white transition-all flex items-center justify-center gap-3 border border-indigo-500/20 relative z-10 shadow-xl shadow-indigo-600/5">
                <i data-lucide="download" class="w-4 h-4"></i>
                Download Label
            </button>
        </div>

        <div class="flex flex-col gap-4">
              <a href="{{ route('reconciliations.create', ['item_id' => $item->id]) }}" class="w-full py-4 bg-emerald-500/10 text-emerald-500 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] text-center border border-emerald-500/20 hover:bg-emerald-500 hover:text-white transition-all flex items-center justify-center gap-3 shadow-xl shadow-emerald-500/5">
                 <i data-lucide="clipboard-check" class="w-5 h-5"></i>
                 Audit Stok Fisik
              </a>
              <a href="{{ route('items.print-label', $item->id) }}" target="_blank" class="w-full py-4 glass text-slate-900 dark:text-white rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] text-center border border-white/20 hover:bg-white/10 transition-all flex items-center justify-center gap-3 shadow-xl">
                 <i data-lucide="printer" class="w-5 h-5"></i>
                 Print Barcode
              </a>
              <a href="{{ route('items.edit', $item->id) }}" class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] text-center shadow-2xl shadow-indigo-600/30 hover:bg-indigo-700 transition-all flex items-center justify-center gap-3">
                <i data-lucide="edit" class="w-5 h-5"></i>
                Edit Detail Barang
             </a>
             <form action="{{ route('items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus barang ini?')" class="w-full">
                @csrf @method('DELETE')
                <button type="submit" class="w-full py-4 bg-rose-500/10 text-rose-500 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-rose-500 hover:text-white transition-all border border-rose-500/20 flex items-center justify-center gap-3 shadow-xl shadow-rose-500/5">
                    <i data-lucide="trash-2" class="w-5 h-5"></i>
                    Hapus Inventaris
                </button>
             </form>
        </div>

        <!-- Team Collaboration Notes -->
        <div class="glass p-8 rounded-[2.5rem] border border-white/20 dark:border-white/5 shadow-2xl relative overflow-hidden">
            <h3 class="text-xs font-black text-slate-900 dark:text-white mb-6 flex items-center gap-2 uppercase tracking-[0.2em]">
                <i data-lucide="message-square" class="w-4 h-4 text-indigo-500"></i>
                Team Notes
            </h3>
            <div class="space-y-4 max-h-60 overflow-y-auto mb-6 pr-2 custom-scrollbar">
                @foreach($item->notes as $note)
                <div class="p-4 bg-white/5 dark:bg-white/5 rounded-2xl border border-white/10 group">
                    <p class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed">{{ $note->content }}</p>
                    <div class="flex justify-between items-center mt-3 pt-3 border-t border-white/5">
                        <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">{{ $note->user->name }} • {{ $note->created_at->diffForHumans() }}</span>
                        @if(Auth::id() === $note->user_id)
                        <form action="{{ route('notes.destroy', $note->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button class="text-rose-500/50 hover:text-rose-500 transition-colors">
                                <i data-lucide="trash-2" class="w-3 h-3"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            <form action="{{ route('notes.store') }}" method="POST">
                @csrf
                <input type="hidden" name="noteable_id" value="{{ $item->id }}">
                <input type="hidden" name="noteable_type" value="{{ get_class($item) }}">
                <div class="relative">
                    <input type="text" name="content" class="w-full bg-white/10 dark:bg-slate-900/50 border border-white/20 dark:border-white/10 rounded-2xl px-5 py-4 text-[10px] font-bold text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500 transition-all" placeholder="Tambah catatan tim...">
                    <button type="submit" class="absolute right-3 top-3 w-8 h-8 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-all flex items-center justify-center shadow-lg shadow-indigo-600/30">
                        <i data-lucide="send" class="w-3.5 h-3.5"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Batches Section -->
<div class="mt-12">
    <div class="glass p-10 rounded-[3rem] border border-white/20 dark:border-white/5 shadow-2xl overflow-hidden mb-12">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Batch & Expiry Tracking</h3>
                <p class="text-xs text-slate-500 mt-1">Lacak stok berdasarkan nomor batch dan tanggal kadaluarsa.</p>
            </div>
            <button onclick="document.getElementById('batchForm').classList.toggle('hidden')" class="px-5 py-2.5 bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-black uppercase tracking-widest border border-slate-200 dark:border-white/10 hover:bg-indigo-600 hover:text-white transition-all">
                Add New Batch
            </button>
        </div>

        <div id="batchForm" class="hidden mb-8 p-6 bg-indigo-500/5 rounded-3xl border border-indigo-500/10">
            <form action="{{ route('batches.store', $item->id) }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                @csrf
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 px-1">Batch Number</label>
                    <input type="text" name="batch_number" class="w-full bg-white/80 dark:bg-slate-900/80 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-xs" required placeholder="BN-2026-001">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 px-1">Quantity</label>
                    <input type="number" name="quantity" class="w-full bg-white/80 dark:bg-slate-900/80 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-xs" required placeholder="0.00">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 px-1">Expiry Date</label>
                    <input type="date" name="expiry_date" class="w-full bg-white/80 dark:bg-slate-900/80 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-xs">
                </div>
                <button type="submit" class="bg-indigo-600 text-white h-[42px] rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-indigo-700 transition-all">
                    Register Batch
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-white/5 border-b border-white/10">
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Batch Number</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Status</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Quantity</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Expiry</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-white/5">
                    @foreach($item->batches as $batch)
                    <tr class="group">
                        <td class="px-6 py-4 font-mono text-sm text-indigo-600 dark:text-indigo-400">{{ $batch->batch_number }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($batch->expiry_date && \Carbon\Carbon::parse($batch->expiry_date)->isPast())
                                <span class="px-2 py-0.5 bg-rose-500 text-white text-[9px] font-black uppercase rounded-md">Expired</span>
                            @elseif($batch->expiry_date && \Carbon\Carbon::parse($batch->expiry_date)->diffInDays() < 30)
                                <span class="px-2 py-0.5 bg-amber-500 text-white text-[9px] font-black uppercase rounded-md">Exp Soon</span>
                            @else
                                <span class="px-2 py-0.5 bg-emerald-500 text-white text-[9px] font-black uppercase rounded-md">Fresh</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-bold text-slate-900 dark:text-white">{{ $batch->quantity }}</td>
                        <td class="px-6 py-4 text-xs text-slate-500">{{ $batch->expiry_date ? \Carbon\Carbon::parse($batch->expiry_date)->format('d M Y') : 'N/A' }}</td>
                        <td class="px-6 py-4 text-right">
                            <form action="{{ route('batches.destroy', $batch->id) }}" method="POST" onsubmit="return confirm('Hapus batch ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-slate-400 hover:text-rose-500 transition-colors">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                    @if($item->batches->isEmpty())
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-slate-400 italic text-sm">No batch data available for this item.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    {{-- Interactive Activity Timeline --}}
    <div class="glass border border-white/20 dark:border-white/5 rounded-[3rem] p-10 shadow-2xl overflow-hidden mb-12">
        <div class="flex items-center justify-between mb-10">
            <div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Activity Timeline</h3>
                <p class="text-xs text-slate-500 font-medium">Visual history of changes and movements for this item.</p>
            </div>
            <div class="p-3 bg-indigo-500/10 rounded-2xl">
                <i data-lucide="history" class="w-6 h-6 text-indigo-600"></i>
            </div>
        </div>

        <div class="relative">
            {{-- Vertical Line --}}
            <div class="absolute left-[11px] top-2 bottom-2 w-0.5 bg-slate-100 dark:bg-white/5"></div>

            <div class="space-y-10 relative">
                @foreach($activities as $activity)
                <div class="flex gap-6 group">
                    <div class="relative z-10">
                        <div class="w-6 h-6 rounded-full bg-white dark:bg-slate-900 border-4 border-indigo-600 group-hover:scale-125 transition-transform duration-300"></div>
                    </div>
                    <div class="flex-1 -mt-1">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <span class="block text-xs font-black uppercase tracking-widest text-indigo-600 mb-1">{{ $activity->description }}</span>
                                <p class="text-sm text-slate-700 dark:text-slate-300">
                                    @if($activity->causer)
                                        <span class="font-bold">{{ $activity->causer->name }}</span>
                                    @else
                                        <span class="font-bold">System</span>
                                    @endif
                                    @if(isset($activity->properties['attributes']))
                                        modified: <span class="text-xs font-mono text-slate-400">{{ implode(', ', array_keys($activity->properties['attributes'])) }}</span>
                                    @endif
                                </p>
                            </div>
                            <span class="text-[10px] font-bold text-slate-400 bg-slate-100 dark:bg-white/5 px-2 py-1 rounded-lg">
                                {{ $activity->created_at->diffForHumans() }}
                            </span>
                        </div>
                        
                        @if(isset($activity->properties['old']) || isset($activity->properties['attributes']))
                        <div class="mt-3 p-4 bg-slate-50/50 dark:bg-white/5 rounded-2xl border border-slate-100 dark:border-white/5 text-[10px] font-mono overflow-x-auto">
                            <div class="grid grid-cols-2 gap-4">
                                @if(isset($activity->properties['old']))
                                <div>
                                    <span class="block text-rose-500 font-bold mb-1 uppercase">Before</span>
                                    @foreach($activity->properties['old'] as $key => $val)
                                        <div class="text-slate-400"><span class="text-slate-500">{{ $key }}:</span> {{ $val }}</div>
                                    @endforeach
                                </div>
                                @endif
                                @if(isset($activity->properties['attributes']))
                                <div>
                                    <span class="block text-emerald-500 font-bold mb-1 uppercase">After</span>
                                    @foreach($activity->properties['attributes'] as $key => $val)
                                        <div class="text-slate-400"><span class="text-slate-500">{{ $key }}:</span> {{ $val }}</div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach

                @if($activities->isEmpty())
                <div class="text-center py-10">
                    <p class="text-sm text-slate-400 italic">No activity recorded yet.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
<script>
    const qrcode = new QRCode(document.getElementById("qrcode"), {
        text: "{{ route('items.show', $item->id) }}",
        width: 200,
        height: 200,
        colorDark : "#0f172a",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });

    function downloadQR() {
        const img = document.querySelector("#qrcode img");
        if (img) {
            const link = document.createElement("a");
            link.href = img.src;
            link.download = "QR_{{ $item->kode_barang }}.png";
            link.click();
        }
    }
</script>
@endpush
@endsection
