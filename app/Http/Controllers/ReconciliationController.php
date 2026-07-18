<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Reconciliation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReconciliationController extends Controller
{
    public function index()
    {
        $reconciliations = Reconciliation::with(['item', 'user'])->latest()->paginate(20);
        return view('reconciliations.index', compact('reconciliations'));
    }

    public function create(Request $request)
    {
        $items = Item::all();
        $selectedItem = $request->has('item_id') ? Item::find($request->item_id) : null;
        return view('reconciliations.create', compact('items', 'selectedItem'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'physical_stock' => 'required|numeric|min:0',
            'reason' => 'nullable|string'
        ]);

        return DB::transaction(function () use ($request) {
            $item = Item::findOrFail($request->item_id);
            $systemStock = $item->stok_barang;
            $physicalStock = $request->physical_stock;
            $difference = $physicalStock - $systemStock;

            $reconciliation = Reconciliation::create([
                'item_id' => $item->id,
                'user_id' => auth()->id(),
                'system_stock' => $systemStock,
                'physical_stock' => $physicalStock,
                'difference' => $difference,
                'reason' => $request->reason,
                'status' => 'completed'
            ]);

            // Update item stock directly
            $item->update(['stok_barang' => $physicalStock]);

            return redirect()->route('reconciliations.index')
                ->with('success', "Stock reconciliation for {$item->nama_barang} completed successfully.");
        });
    }
}
