<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\Transaction;
use App\Models\PurchaseOrder;
use App\Models\Batch;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ZenithRealisticSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Categories
        $categories = [
            'Elektronik' => 'Perangkat keras, gadget, dan komponen IT.',
            'Office Supplies' => 'Kebutuhan operasional kantor sehari-hari.',
            'Pantry & FMCG' => 'Makanan, minuman, dan kebutuhan dapur kantor.',
            'Furniture' => 'Meja, kursi, dan perlengkapan interior.',
            'Medical' => 'Kebutuhan kesehatan dan P3K.'
        ];

        foreach ($categories as $name => $desc) {
            Category::firstOrCreate(['name' => $name], ['description' => $desc]);
        }

        // 2. Units
        $units = [
            ['name' => 'Pieces', 'symbol' => 'pcs'],
            ['name' => 'Box', 'symbol' => 'box'],
            ['name' => 'Kilogram', 'symbol' => 'kg'],
            ['name' => 'Pack', 'symbol' => 'pack'],
            ['name' => 'Unit', 'symbol' => 'unit'],
        ];

        foreach ($units as $u) {
            Unit::firstOrCreate(['symbol' => $u['symbol']], ['name' => $u['name']]);
        }

        // 3. Suppliers
        $suppliers = [
            ['name' => 'Global Tech Corp', 'email' => 'sales@globaltech.com', 'phone' => '021-555-0192', 'address' => 'Sudirman Central Business District'],
            ['name' => 'Office Depot Indo', 'email' => 'support@officedepot.id', 'phone' => '021-444-0188', 'address' => 'Kawasan Industri Pulogadung'],
            ['name' => 'Medix Solutions', 'email' => 'info@medix.com', 'phone' => '021-333-0177', 'address' => 'Kuningan Health Center'],
            ['name' => 'Furniture Direct', 'email' => 'hello@furnidirect.com', 'phone' => '021-222-0166', 'address' => 'BSD City Enterprise Park']
        ];

        foreach ($suppliers as $s) {
            Supplier::firstOrCreate(['name' => $s['name']], $s);
        }

        // 4. Warehouses
        $warehouses = [
            ['name' => 'Main Warehouse (WH-A)', 'location' => 'Jakarta Utara'],
            ['name' => 'Satellite Hub (WH-B)', 'location' => 'Bekasi Barat']
        ];

        foreach ($warehouses as $w) {
            Warehouse::firstOrCreate(['name' => $w['name']], $w);
        }

        $cats = Category::all()->keyBy('name');
        $unts = Unit::all()->keyBy('symbol');
        $sups = Supplier::all()->keyBy('name');
        $whs = Warehouse::all();

        // 5. Realistic Items Data
        $itemsData = [
            // ELEKTRONIK
            [
                'nama_barang' => 'MacBook Pro M3 Max 14"',
                'kode_barang' => 'ELC-MBP-001',
                'harga_barang' => 38000000,
                'selling_price' => 45500000,
                'stok_barang' => 12,
                'min_stock' => 3,
                'cat' => 'Elektronik',
                'unit' => 'pcs',
                'sup' => 'Global Tech Corp'
            ],
            [
                'nama_barang' => 'Monitor Dell UltraSharp 27"',
                'kode_barang' => 'ELC-MON-002',
                'harga_barang' => 6500000,
                'selling_price' => 8200000,
                'stok_barang' => 25,
                'min_stock' => 5,
                'cat' => 'Elektronik',
                'unit' => 'pcs',
                'sup' => 'Global Tech Corp'
            ],
            [
                'nama_barang' => 'Mechanical Keyboard V3',
                'kode_barang' => 'ELC-KBD-003',
                'harga_barang' => 1200000,
                'selling_price' => 1850000,
                'stok_barang' => 45,
                'min_stock' => 10,
                'cat' => 'Elektronik',
                'unit' => 'pcs',
                'sup' => 'Global Tech Corp'
            ],

            // OFFICE SUPPLIES
            [
                'nama_barang' => 'Kertas A4 PaperOne 80gr',
                'kode_barang' => 'OFF-PPR-001',
                'harga_barang' => 48000,
                'selling_price' => 58000,
                'stok_barang' => 120,
                'min_stock' => 50,
                'cat' => 'Office Supplies',
                'unit' => 'box',
                'sup' => 'Office Depot Indo'
            ],
            [
                'nama_barang' => 'Tinta HP 680 Black',
                'kode_barang' => 'OFF-INK-002',
                'harga_barang' => 145000,
                'selling_price' => 175000,
                'stok_barang' => 15,
                'min_stock' => 20, // LOW STOCK TRIGGER
                'cat' => 'Office Supplies',
                'unit' => 'pcs',
                'sup' => 'Office Depot Indo'
            ],

            // PANTRY
            [
                'nama_barang' => 'Kopi Arabica Gayo 1kg',
                'kode_barang' => 'FMCG-COF-001',
                'harga_barang' => 185000,
                'selling_price' => 245000,
                'stok_barang' => 30,
                'min_stock' => 5,
                'cat' => 'Pantry & FMCG',
                'unit' => 'kg',
                'sup' => 'Office Depot Indo'
            ],

            // FURNITURE (Assets)
            [
                'nama_barang' => 'Ergonomic Mesh Chair X1',
                'kode_barang' => 'FNT-CHR-001',
                'harga_barang' => 2400000,
                'selling_price' => 3100000,
                'stok_barang' => 8,
                'min_stock' => 2,
                'cat' => 'Furniture',
                'unit' => 'unit',
                'sup' => 'Furniture Direct',
                'is_asset' => true,
                'purchase_date' => Carbon::now()->subMonths(12),
                'useful_life_months' => 36,
                'salvage_value' => 500000
            ],
            [
                'nama_barang' => 'Standing Desk Pro',
                'kode_barang' => 'FNT-DSK-002',
                'harga_barang' => 4500000,
                'selling_price' => 5800000,
                'stok_barang' => 5,
                'min_stock' => 2,
                'cat' => 'Furniture',
                'unit' => 'unit',
                'sup' => 'Furniture Direct',
                'is_asset' => true,
                'purchase_date' => Carbon::now()->subMonths(6),
                'useful_life_months' => 48,
                'salvage_value' => 1000000
            ],

            // MEDICAL
            [
                'nama_barang' => 'First Aid Kit Premium',
                'kode_barang' => 'MED-FAK-001',
                'harga_barang' => 350000,
                'selling_price' => 475000,
                'stok_barang' => 20,
                'min_stock' => 5,
                'cat' => 'Medical',
                'unit' => 'box',
                'sup' => 'Medix Solutions'
            ],
            [
                'nama_barang' => 'Hand Sanitizer 5L',
                'kode_barang' => 'MED-HS-002',
                'harga_barang' => 120000,
                'selling_price' => 165000,
                'stok_barang' => 0, // OUT OF STOCK
                'min_stock' => 10,
                'cat' => 'Medical',
                'unit' => 'pcs',
                'sup' => 'Medix Solutions'
            ],
        ];

        foreach ($itemsData as $data) {
            $item = Item::updateOrCreate(
                ['kode_barang' => $data['kode_barang']],
                [
                    'nama_barang' => $data['nama_barang'],
                    'harga_barang' => $data['harga_barang'],
                    'selling_price' => $data['selling_price'],
                    'stok_barang' => $data['stok_barang'],
                    'min_stock' => $data['min_stock'],
                    'category_id' => $cats[$data['cat']]->id,
                    'unit_id' => $unts[$data['unit']]->id,
                    'supplier_id' => $sups[$data['sup']]->id,
                    'is_asset' => $data['is_asset'] ?? false,
                    'purchase_date' => $data['purchase_date'] ?? null,
                    'useful_life_months' => $data['useful_life_months'] ?? null,
                    'salvage_value' => $data['salvage_value'] ?? null,
                ]
            );

            // 6. Generate 30 days of Transactions
            for ($i = 30; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                
                // Random IN transactions (Restock)
                if (rand(1, 10) > 8) { // 20% chance each day
                    Transaction::create([
                        'item_id' => $item->id,
                        'warehouse_id' => $whs->random()->id,
                        'type' => 'in',
                        'quantity' => rand(5, 20),
                        'note' => 'Weekly Restock - Batch ' . $date->format('Ym'),
                        'created_at' => $date->copy()->hour(rand(8, 11)),
                    ]);
                }

                // Random OUT transactions (Sales/Usage)
                $outProb = $data['cat'] === 'Office Supplies' ? 6 : 9; // Office supplies move faster
                if (rand(1, 10) > $outProb) { 
                    Transaction::create([
                        'item_id' => $item->id,
                        'warehouse_id' => $whs->random()->id,
                        'type' => 'out',
                        'quantity' => rand(1, 5),
                        'note' => 'Order fulfilled via Smart POS',
                        'created_at' => $date->copy()->hour(rand(13, 17)),
                    ]);
                }
            }

            // 7. Batches for FMCG/Medical
            if (in_array($data['cat'], ['Pantry & FMCG', 'Medical'])) {
                Batch::create([
                    'item_id' => $item->id,
                    'warehouse_id' => $whs->first()->id,
                    'batch_number' => 'BTCH-' . $item->id . '-EXP',
                    'quantity' => $item->stok_barang,
                    'expiry_date' => Carbon::now()->addDays(rand(10, 365))
                ]);
            }
        }

        // 8. Create some Purchase Orders
        for ($j = 1; $j <= 5; $j++) {
            $sup = $sups->random();
            $poItems = [
                ['id' => 1, 'name' => 'Dummy Item', 'qty' => 10] // Placeholder
            ];
            
            // Get actual items for this supplier
            $actualItems = Item::where('supplier_id', $sup->id)->take(2)->get();
            if ($actualItems->count() > 0) {
                $poItems = $actualItems->map(function($it) {
                    return ['id' => $it->id, 'name' => $it->nama_barang, 'qty' => rand(10, 50)];
                })->toArray();
            }

            PurchaseOrder::create([
                'supplier_id' => $sup->id,
                'po_number' => 'PO-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2))),
                'status' => ['draft', 'sent', 'received'][rand(0, 2)],
                'total_amount' => rand(5000000, 50000000),
                'expected_date' => Carbon::now()->addDays(rand(3, 7)),
                'items' => $poItems,
                'note' => 'Enterprise realistic procurement simulation.'
            ]);
        }
    }
}
