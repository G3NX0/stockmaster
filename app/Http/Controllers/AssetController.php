<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index()
    {
        $assets = Item::where('is_asset', true)
            ->with(['category', 'unit'])
            ->latest()
            ->get();

        return view('assets.index', compact('assets'));
    }

    public function toggle(Item $item)
    {
        $item->update(['is_asset' => !$item->is_asset]);
        $status = $item->is_asset ? 'marked as Asset' : 'marked as Inventory';
        return back()->with('success', "Item has been $status.");
    }
}
