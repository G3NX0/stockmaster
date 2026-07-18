<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ItemImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $category = Category::firstOrCreate(['name' => $row['kategori']]);
        $unit = Unit::firstOrCreate(['name' => $row['satuan']]);
        $supplier = Supplier::firstOrCreate(['name' => $row['supplier']]);

        return new Item([
            'kode_barang' => $row['sku'],
            'nama_barang' => $row['nama_barang'],
            'harga_barang' => $row['harga'],
            'stok_barang' => $row['stok'],
            'min_stock' => $row['min_stok'] ?? 5,
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'supplier_id' => $supplier->id,
        ]);
    }

    public function rules(): array
    {
        return [
            'sku' => 'required|unique:items,kode_barang',
            'nama_barang' => 'required',
            'harga' => 'required|numeric',
            'stok' => 'required|numeric',
            'kategori' => 'required',
            'satuan' => 'required',
            'supplier' => 'required',
        ];
    }
}
