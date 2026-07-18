<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        $units = \App\Models\Unit::all();
        return view('units.index', compact('units'));
    }

    public function create()
    {
        return view('units.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:units', 'symbol' => 'required|unique:units']);
        \App\Models\Unit::create($request->all());
        return redirect()->route('units.index')->with('success', 'Satuan berhasil ditambahkan.');
    }

    public function edit(\App\Models\Unit $unit)
    {
        return view('units.edit', compact('unit'));
    }

    public function update(Request $request, \App\Models\Unit $unit)
    {
        $request->validate(['name' => 'required|unique:units,name,' . $unit->id, 'symbol' => 'required|unique:units,symbol,' . $unit->id]);
        $unit->update($request->all());
        return redirect()->route('units.index')->with('success', 'Satuan berhasil diupdate.');
    }

    public function destroy(\App\Models\Unit $unit)
    {
        $unit->delete();
        return redirect()->route('units.index')->with('success', 'Satuan berhasil dihapus.');
    }

}
