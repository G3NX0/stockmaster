<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Supplier;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Spatie\SimpleExcel\SimpleExcelReader;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ItemController extends Controller
{
    public function exportPdf()
    {
        $items = Item::with(['category', 'unit', 'supplier'])->get();
        $pdf = Pdf::loadView('items.pdf', compact('items'))->setPaper('a4', 'landscape');
        return $pdf->download('inventory-report.pdf');
    }

    public function exportExcel()
    {
        $items = Item::with(['category', 'unit', 'supplier'])->get();
        
        $writer = SimpleExcelWriter::streamDownload('inventory-report.xlsx');
        
        foreach ($items as $item) {
            $writer->addRow([
                'SKU' => $item->kode_barang,
                'Nama Barang' => $item->nama_barang,
                'Harga' => $item->harga_barang,
                'Stok' => $item->stok_barang,
                'Min Stok' => $item->min_stock,
                'Kategori' => $item->category?->name ?? '-',
                'Satuan' => $item->unit?->name ?? '-',
                'Supplier' => $item->supplier?->name ?? '-',
            ]);
        }

        return $writer->toBrowser();
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls',
        ]);

        try {
            $file = $request->file('file');
            $tempPath = $file->getRealPath();
            $extension = $file->getClientOriginalExtension();
            
            // Create a temporary file with the correct extension for SimpleExcelReader
            $securePath = storage_path('app/temp_' . time() . '.' . $extension);
            copy($tempPath, $securePath);
            
            $reader = SimpleExcelReader::create($securePath)->getRows();
            
            $reader->each(function(array $row) {
                // Find or create relations
                $category = Category::firstOrCreate(['name' => $row['Kategori'] ?? $row['kategori'] ?? 'Lainnya']);
                $unitName = $row['Satuan'] ?? $row['satuan'] ?? 'Pcs';
                $unit = Unit::firstOrCreate(
                    ['name' => $unitName],
                    ['symbol' => strtolower($unitName)]
                );
                
                $supplier = Supplier::firstOrCreate(['name' => $row['Supplier'] ?? $row['supplier'] ?? 'Internal']);

                Item::updateOrCreate(
                    ['kode_barang' => $row['SKU'] ?? $row['sku']],
                    [
                        'nama_barang' => $row['Nama Barang'] ?? $row['nama_barang'],
                        'harga_barang' => $row['Harga'] ?? $row['harga'],
                        'stok_barang' => $row['Stok'] ?? $row['stok'],
                        'min_stock' => $row['Min Stok'] ?? $row['min_stok'] ?? 5,
                        'category_id' => $category->id,
                        'unit_id' => $unit->id,
                        'supplier_id' => $supplier->id,
                    ]
                );
            });

            return redirect()->route('items.index')->with('success', 'Data barang berhasil diimport.');
        } catch (\Exception $e) {
            return redirect()->route('items.index')->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }

    public function index(\App\Services\PredictionService $predictionService)
    {
        $items = Item::with(['category', 'unit', 'supplier'])->latest()->get();
        $categories = Category::all();
        
        $items->each(function($item) use ($predictionService) {
            $item->prediction = $predictionService->predictDaysLeft($item);
        });

        return view('items.index', compact('items', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        $units = Unit::all();
        $suppliers = Supplier::all();
        return view('items.create', compact('categories', 'units', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required',
            'harga_barang' => 'required|numeric',
            'stok_barang' => 'required|numeric',
            'kode_barang' => 'required|unique:items',
            'category_id' => 'nullable|exists:categories,id',
            'unit_id' => 'nullable|exists:units,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'min_stock' => 'required|numeric|min:0',
        ]);

        Item::create($request->all());

        return redirect()->route('items.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit(Item $item)
    {
        $categories = Category::all();
        $units = Unit::all();
        $suppliers = Supplier::all();
        return view('items.edit', compact('item', 'categories', 'units', 'suppliers'));
    }

    public function update(Request $request, Item $item)
    {
        $request->validate([
            'nama_barang' => 'required',
            'harga_barang' => 'required|numeric',
            'stok_barang' => 'required|numeric',
            'kode_barang' => 'required|unique:items,kode_barang,' . $item->id,
            'category_id' => 'nullable|exists:categories,id',
            'unit_id' => 'nullable|exists:units,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'min_stock' => 'required|numeric|min:0',
        ]);

        $item->update($request->all());

        return redirect()->route('items.index')->with('success', 'Barang berhasil diperbarui.');
    }

    public function show(Item $item, \App\Services\PredictionService $predictionService)
    {
        $item->load(['category', 'unit', 'supplier', 'batches.warehouse', 'notes.user']);
        $prediction = $predictionService->predictDaysLeft($item) ?? '∞';
        $activities = $item->activities()->latest()->limit(10)->get();

        return view('items.show', compact('item', 'prediction', 'activities'));
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('items.index')->with('success', 'Barang berhasil dihapus.');
    }

    public function printLabel(Item $item)
    {
        $item->load(['category', 'unit', 'supplier']);
        $pdf = Pdf::loadView('labels.item', compact('item'))
            ->setPaper([0, 0, 141.73, 85.04]); // 50mm x 30mm exact
        
        return $pdf->stream("label-{$item->kode_barang}.pdf");
    }
}
