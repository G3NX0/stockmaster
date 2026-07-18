<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\CustomerCategory;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\User;
use Illuminate\Database\Seeder;

class Tier3EnterpriseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Roles & Users
        User::updateOrCreate(['email' => 'finance@stockmaster.com'], [
            'name' => 'Finance Manager',
            'password' => bcrypt('password'),
            'role' => 'finance'
        ]);
        User::updateOrCreate(['email' => 'warehouse@stockmaster.com'], [
            'name' => 'Warehouse Lead',
            'password' => bcrypt('password'),
            'role' => 'warehouse'
        ]);

        // 2. Create Warehouses
        $wh1 = Warehouse::firstOrCreate(['name' => 'Main Distribution Center'], ['location' => 'Jakarta Industrial Zone']);
        $wh2 = Warehouse::firstOrCreate(['name' => 'Sub-Warehouse North'], ['location' => 'Medan Logistics Hub']);

        // 3. Create Customer Categories
        $retail = CustomerCategory::updateOrCreate(['name' => 'Retail'], ['discount_percent' => 0]);
        $wholesale = CustomerCategory::updateOrCreate(['name' => 'Wholesale'], ['discount_percent' => 15]);
        $vip = CustomerCategory::updateOrCreate(['name' => 'VIP'], ['discount_percent' => 25]);

        // 4. Create Customers
        Customer::create([
            'customer_category_id' => $retail->id,
            'name' => 'IndoMart Retail',
            'email' => 'contact@indomart.com',
            'phone' => '021-555-1234'
        ]);
        Customer::create([
            'customer_category_id' => $wholesale->id,
            'name' => 'Global Trading Corp',
            'email' => 'sales@globaltrading.com',
            'phone' => '021-999-8888'
        ]);

        // 5. Create Items with Enterprise Pricing
        $cat = Category::first();
        Item::create([
            'nama_barang' => 'Enterprise Server R740',
            'kode_barang' => 'SRV-R740',
            'harga_barang' => 45000000,
            'selling_price' => 55000000,
            'wholesale_price' => 50000000,
            'promo_price' => 48000000,
            'promo_start_date' => now(),
            'promo_end_date' => now()->addDays(30),
            'stok_barang' => 5,
            'min_stock' => 2,
            'category_id' => $cat->id,
            'supplier_id' => Supplier::first()?->id
        ]);

        $item2 = Item::create([
            'nama_barang' => 'Cisco Catalyst Switch',
            'kode_barang' => 'SW-CISCO-24',
            'harga_barang' => 12000000,
            'selling_price' => 18000000,
            'stok_barang' => 1,
            'min_stock' => 3, // This will trigger PO generator
            'category_id' => $cat->id,
            'supplier_id' => Supplier::first()?->id
        ]);

        // 6. Create Historical Transactions for AI Forecasting
        \App\Models\Transaction::create([
            'item_id' => $item2->id,
            'type' => 'out',
            'quantity' => 20,
            'user_id' => User::first()->id,
            'created_at' => now()->subDays(10)
        ]);

        // 7. Create Batches for Expiring Alerts
        \App\Models\Batch::create([
            'item_id' => $item2->id,
            'batch_number' => 'BCH-2024-X1',
            'quantity' => 10,
            'expiry_date' => now()->addDays(5) // Critical expiring
        ]);

        \App\Models\Batch::create([
            'item_id' => $item2->id,
            'batch_number' => 'BCH-2024-X2',
            'quantity' => 15,
            'expiry_date' => now()->addDays(45) // Warning zone
        ]);
    }
}
