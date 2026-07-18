<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Item;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    public function store(Request $request, Item $item)
    {
        $request->validate([
            'batch_number' => 'required|string',
            'quantity' => 'required|numeric|min:0',
            'expiry_date' => 'nullable|date',
            'warehouse_id' => 'nullable|exists:warehouses,id'
        ]);

        $item->batches()->create($request->all());

        return back()->with('success', 'Batch successfully added.');
    }

    public function destroy(Batch $batch)
    {
        $batch->delete();
        return back()->with('success', 'Batch deleted.');
    }
}
