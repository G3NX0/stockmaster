<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use App\Models\Item;
use App\Models\Warehouse;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    public function index()
    {
        $transfers = Transfer::with(['item', 'fromWarehouse', 'toWarehouse', 'user'])->latest()->get();
        $items = Item::all();
        $warehouses = Warehouse::all();
        return view('transfers.index', compact('transfers', 'items', 'warehouses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id|different:from_warehouse_id',
            'quantity' => 'required|integer|min:1',
            'note' => 'nullable|string',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'completed'; // Simplified for this implementation

        DB::transaction(function () use ($validated) {
            // 1. Create Transfer Record
            Transfer::create($validated);

            // 2. Create 'Out' Transaction for Source
            Transaction::create([
                'item_id' => $validated['item_id'],
                'warehouse_id' => $validated['from_warehouse_id'],
                'type' => 'out',
                'quantity' => $validated['quantity'],
                'note' => "Transfer to Warehouse ID: " . $validated['to_warehouse_id'],
            ]);

            // 3. Create 'In' Transaction for Destination
            Transaction::create([
                'item_id' => $validated['item_id'],
                'warehouse_id' => $validated['to_warehouse_id'],
                'type' => 'in',
                'quantity' => $validated['quantity'],
                'note' => "Transfer from Warehouse ID: " . $validated['from_warehouse_id'],
            ]);

            // Note: Total item stock remains same, but warehouse-level distribution changes.
            // If the system tracks per-warehouse stock in a separate table, update it here.
        });

        return redirect()->route('transfers.index')->with('success', 'Transfer completed successfully.');
    }
}
