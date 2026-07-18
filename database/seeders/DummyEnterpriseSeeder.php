<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\Batch;
use Carbon\Carbon;

class DummyEnterpriseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Categories
        $electronics = Category::firstOrCreate(['name' => 'Elektronik']);
        $furniture = Category::firstOrCreate(['name' => 'Furniture']);
        $medical = Category::firstOrCreate(['name' => 'Medical Supplies']);

        // 2. Units
        $pcs = Unit::where('symbol', 'pcs')->first() ?: Unit::create(['name' => 'Pieces', 'symbol' => 'pcs']);
        $box = Unit::where('symbol', 'box')->first() ?: Unit::create(['name' => 'Box', 'symbol' => 'box']);

        // 3. Suppliers
        $globalTech = Supplier::firstOrCreate(['name' => 'Global Tech Corp']);
        $medix = Supplier::firstOrCreate(['name' => 'Medix Solutions']);

        // 4. Warehouses
        $mainWh = Warehouse::firstOrCreate(['name' => 'Main Warehouse', 'location' => 'Jakarta']);

        // --- ENTERPRISE ITEMS: PROFIT ANALYTICS ---
        
        // Item with high profit margin
        $laptop = Item::create([
            'nama_barang' => 'MacBook Pro M3',
            'kode_barang' => 'ELC-MBP-01',
            'harga_barang' => 25000000,
            'selling_price' => 32000000, // 21% margin
            'stok_barang' => 15,
            'category_id' => $electronics->id,
            'unit_id' => $pcs->id,
            'supplier_id' => $globalTech->id,
            'min_stock' => 5,
            'is_asset' => false
        ]);

        $monitor = Item::create([
            'nama_barang' => 'Dell UltraSharp 27',
            'kode_barang' => 'ELC-DEL-27',
            'harga_barang' => 5000000,
            'selling_price' => 7500000, // 33% margin
            'stok_barang' => 20,
            'category_id' => $electronics->id,
            'unit_id' => $pcs->id,
            'supplier_id' => $globalTech->id,
            'min_stock' => 5
        ]);

        // --- ENTERPRISE ITEMS: ASSET DEPRECIATION ---

        Item::create([
            'nama_barang' => 'Office Server Rack',
            'kode_barang' => 'AST-SRV-01',
            'harga_barang' => 50000000,
            'stok_barang' => 1,
            'category_id' => $electronics->id,
            'unit_id' => $pcs->id,
            'supplier_id' => $globalTech->id,
            'is_asset' => true,
            'purchase_date' => Carbon::now()->subMonths(18),
            'useful_life_months' => 48,
            'salvage_value' => 5000000
        ]);

        Item::create([
            'nama_barang' => 'Company Van',
            'kode_barang' => 'AST-VAN-02',
            'harga_barang' => 350000000,
            'stok_barang' => 1,
            'category_id' => $furniture->id, // Simplified category
            'unit_id' => $pcs->id,
            'is_asset' => true,
            'purchase_date' => Carbon::now()->subMonths(6),
            'useful_life_months' => 60,
            'salvage_value' => 50000000
        ]);

        // --- ENTERPRISE ITEMS: EXPIRING BATCHES ---

        $vaccine = Item::create([
            'nama_barang' => 'Flu Vaccine G2',
            'kode_barang' => 'MED-VAC-01',
            'harga_barang' => 150000,
            'selling_price' => 250000,
            'stok_barang' => 500,
            'category_id' => $medical->id,
            'unit_id' => $box->id,
            'supplier_id' => $medix->id,
            'min_stock' => 100
        ]);

        // Expiring very soon (15 days)
        Batch::create([
            'item_id' => $vaccine->id,
            'warehouse_id' => $mainWh->id,
            'batch_number' => 'VAC-B01-EXP',
            'quantity' => 150,
            'expiry_date' => Carbon::now()->addDays(15)
        ]);

        // Expiring soon (45 days)
        Batch::create([
            'item_id' => $vaccine->id,
            'warehouse_id' => $mainWh->id,
            'batch_number' => 'VAC-B02-MID',
            'quantity' => 200,
            'expiry_date' => Carbon::now()->addDays(45)
        ]);

        // Long expiry (2 years)
        Batch::create([
            'item_id' => $vaccine->id,
            'warehouse_id' => $mainWh->id,
            'batch_number' => 'VAC-B03-LONG',
            'quantity' => 150,
            'expiry_date' => Carbon::now()->addYears(2)
        ]);
    }
}
