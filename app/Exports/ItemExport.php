<?php

namespace App\Exports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ItemExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Item::with(['category', 'unit', 'supplier'])->get();
    }

    public function headings(): array
    {
        return [
            'SKU',
            'Nama Barang',
            'Harga',
            'Stok',
            'Min Stok',
            'Kategori',
            'Satuan',
            'Supplier',
        ];
    }

    public function map($item): array
    {
        return [
            $item->kode_barang,
            $item->nama_barang,
            $item->harga_barang,
            $item->stok_barang,
            $item->min_stock,
            $item->category?->name ?? '-',
            $item->unit?->name ?? '-',
            $item->supplier?->name ?? '-',
        ];
    }
}
