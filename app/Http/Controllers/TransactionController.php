<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Item;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['item', 'customer', 'user'])->latest('id')->get();
        $items = Item::all();
        $customers = Customer::all();
        return view('transactions.index', compact('transactions', 'items', 'customers'));
    }

    public function create()
    {
        $items = \App\Models\Item::all();
        return view('transactions.create', compact('items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'type' => 'required|in:in,out',
            'quantity' => 'required|integer|min:1',
            'customer_id' => 'nullable|exists:customers,id',
            'note' => 'nullable|string',
        ]);

        $item = \App\Models\Item::findOrFail($request->item_id);

        if ($request->type === 'out' && $item->stok_barang < $request->quantity) {
            return back()->withErrors(['quantity' => 'Stok tidak mencukupi untuk pengeluaran ini.']);
        }

        DB::transaction(function () use ($request, $item) {
            $data = $request->all();
            $data['user_id'] = Auth::id();
            Transaction::create($data);

            if ($request->type === 'in') {
                $item->increment('stok_barang', $request->quantity);
            } else {
                $item->decrement('stok_barang', $request->quantity);
            }
        });

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dicatat.');
    }

}
