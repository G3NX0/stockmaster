<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Warehouse;
use App\Models\Customer;
use App\Models\CustomerCategory;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Tier3EnterpriseTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@stockmaster.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        // Setup basic dependencies
        \App\Models\Category::create(['name' => 'Electronics']);
        $wh1 = Warehouse::create(['name' => 'Gudang Utama']);
        $wh2 = Warehouse::create(['name' => 'Gudang Cabang']);
        $cat = CustomerCategory::create(['name' => 'Wholesale', 'discount_percent' => 10]);
        $supplier = Supplier::create(['name' => 'Global Vendor', 'email' => 'vendor@global.com']);
    }

    /** @test */
    public function it_can_transfer_stock_between_warehouses()
    {
        $item = Item::create([
            'nama_barang' => 'MacBook Pro',
            'kode_barang' => 'MBP001',
            'harga_barang' => 20000000,
            'stok_barang' => 10,
            'category_id' => 1,
        ]);

        $wh1 = Warehouse::where('name', 'Gudang Utama')->first();
        $wh2 = Warehouse::where('name', 'Gudang Cabang')->first();

        $response = $this->actingAs($this->admin)->post(route('transfers.store'), [
            'item_id' => $item->id,
            'from_warehouse_id' => $wh1->id,
            'to_warehouse_id' => $wh2->id,
            'quantity' => 5,
            'note' => 'Pindahan stok internal'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('transfers', [
            'item_id' => $item->id,
            'quantity' => 5,
            'status' => 'completed'
        ]);

        // Verify transactions were created
        $this->assertDatabaseHas('transactions', ['type' => 'out', 'warehouse_id' => $wh1->id, 'quantity' => 5]);
        $this->assertDatabaseHas('transactions', ['type' => 'in', 'warehouse_id' => $wh2->id, 'quantity' => 5]);
    }

    /** @test */
    public function it_calculates_dynamic_pricing_for_wholesale_customers()
    {
        $item = Item::create([
            'nama_barang' => 'iPhone 15',
            'kode_barang' => 'IP15',
            'harga_barang' => 10000000,
            'selling_price' => 15000000,
            'wholesale_price' => 13000000,
            'stok_barang' => 10,
            'category_id' => 1,
        ]);

        $cat = CustomerCategory::where('name', 'Wholesale')->first();
        $customer = Customer::create([
            'customer_category_id' => $cat->id,
            'name' => 'Budi Wholesale',
        ]);

        $this->assertEquals(13000000, $item->getEffectivePrice($customer));
    }

    /** @test */
    public function it_applies_promotional_pricing_when_active()
    {
        $item = Item::create([
            'nama_barang' => 'Promo Item',
            'kode_barang' => 'PRM01',
            'harga_barang' => 1000,
            'selling_price' => 2000,
            'promo_price' => 1500,
            'promo_start_date' => now()->subDay(),
            'promo_end_date' => now()->addDay(),
            'stok_barang' => 10,
            'category_id' => 1,
        ]);

        $this->assertEquals(1500, $item->getEffectivePrice());
    }

    /** @test */
    public function it_generates_auto_purchase_orders_for_low_stock()
    {
        $supplier = Supplier::first();
        Item::create([
            'nama_barang' => 'Low Stock Item',
            'kode_barang' => 'LS01',
            'harga_barang' => 5000,
            'stok_barang' => 2,
            'min_stock' => 5,
            'supplier_id' => $supplier->id
        ]);

        $this->actingAs($this->admin)->get(route('pos.generate'));

        $this->assertDatabaseHas('purchase_orders', [
            'supplier_id' => $supplier->id,
            'status' => 'draft'
        ]);
    }

    /** @test */
    public function it_can_update_po_status()
    {
        $supplier = Supplier::first();
        $po = PurchaseOrder::create([
            'supplier_id' => $supplier->id,
            'po_number' => 'PO-TEST-001',
            'status' => 'draft',
            'items' => [],
            'total_amount' => 1000
        ]);

        $response = $this->actingAs($this->admin)->post(route('pos.update-status', ['po' => $po->id]), [
            'status' => 'sent'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('purchase_orders', [
            'id' => $po->id,
            'status' => 'sent'
        ]);
    }

    /** @test */
    public function it_can_quick_restock_item()
    {
        $item = Item::create([
            'nama_barang' => 'Restock Test',
            'kode_barang' => 'RT01',
            'harga_barang' => 1000,
            'stok_barang' => 1,
            'min_stock' => 5,
            'category_id' => 1
        ]);

        $response = $this->actingAs($this->admin)->get(route('pos.restock', ['item' => $item->id]));

        $response->assertRedirect();
        $this->assertDatabaseHas('purchase_orders', [
            'status' => 'draft',
            'total_amount' => ($item->min_stock * 2) * $item->harga_barang
        ]);
    }

    /** @test */
    public function it_restricts_access_based_on_granular_roles()
    {
        $financeUser = User::create([
            'name' => 'Finance Staff',
            'email' => 'finance@stockmaster.com',
            'password' => bcrypt('password'),
            'role' => 'finance'
        ]);

        // Finance should NOT be able to access Transfers (restricted to Admin in our simplified ACL)
        // Note: In our current routes, we used 'role:admin' for simplicity.
        $response = $this->actingAs($financeUser)->get(route('transfers.index'));
        $response->assertStatus(403);
    }
}
