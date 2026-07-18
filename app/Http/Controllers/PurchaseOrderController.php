<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\Item;
use App\Models\Supplier;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $pos = PurchaseOrder::with('supplier')->latest()->get();
        return view('pos.index', compact('pos'));
    }

    public function generate()
    {
        // 1. Identify low stock items grouped by supplier
        $lowStockItems = Item::whereColumn('stok_barang', '<=', 'min_stock')
            ->whereNotNull('supplier_id')
            ->get()
            ->groupBy('supplier_id');

        if ($lowStockItems->isEmpty()) {
            return redirect()->back()->with('info', 'No low stock items found to generate POs.');
        }

        $count = 0;
        foreach ($lowStockItems as $supplierId => $items) {
            $supplier = Supplier::find($supplierId);
            
            $poData = [
                'supplier_id' => $supplierId,
                'po_number' => 'PO-' . now()->format('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2))),
                'status' => 'draft',
                'items' => $items->map(fn($i) => ['id' => $i->id, 'name' => $i->nama_barang, 'qty' => $i->min_stock * 2])->toArray(),
                'total_amount' => $items->sum(fn($i) => $i->harga_barang * ($i->min_stock * 2)),
                'expected_date' => now()->addDays(7),
            ];

            PurchaseOrder::create($poData);
            $count++;
        }

        return redirect()->route('pos.index')->with('success', "$count Purchase Order drafts generated.");
    }

    public function show(PurchaseOrder $po)
    {
        $purchaseOrder = $po;
        return view('pos.show', compact('purchaseOrder'));
    }

    public function restockItem(Item $item)
    {
        $po = PurchaseOrder::create([
            'supplier_id' => $item->supplier_id ?? Supplier::first()->id,
            'po_number' => 'PO-' . strtoupper(uniqid()),
            'status' => 'draft',
            'items' => [
                ['id' => $item->id, 'name' => $item->nama_barang, 'qty' => $item->min_stock * 2]
            ],
            'total_amount' => ($item->min_stock * 2) * $item->harga_barang
        ]);

        return redirect()->route('pos.show', $po)->with('success', "Draft PO created for {$item->nama_barang}");
    }

    public function updateStatus(Request $request, PurchaseOrder $po)
    {
        $po->update(['status' => $request->status]);
        return redirect()->back()->with('success', 'PO status updated.');
    }
}
