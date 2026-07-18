<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Category;
use App\Models\Unit;
use App\Models\User;
use App\Models\Batch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class EnterpriseUpgradeTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        Category::factory()->create(['name' => 'Electronics']);
        Unit::factory()->create(['name' => 'Piece', 'symbol' => 'pcs']);
    }

    public function test_depreciation_calculation_is_accurate()
    {
        Carbon::setTestNow(Carbon::create(2026, 5, 7));
        
        $item = Item::create([
            'nama_barang' => 'Office Laptop',
            'harga_barang' => 12000000,
            'kode_barang' => 'AST-001',
            'category_id' => 1,
            'unit_id' => 1,
            'is_asset' => true,
            'purchase_date' => Carbon::now()->subMonths(12),
            'useful_life_months' => 24,
            'salvage_value' => 2000000,
            'stok_barang' => 1
        ]);

        // Total depreciation = 10M
        // Monthly = 10M / 24 = 416,666.67
        // After 12 months = 12M - 5M = 7M
        
        $this->assertEquals(7000000, round($item->current_value));
        
        Carbon::setTestNow(); // Reset
    }

    public function test_profit_margin_calculation()
    {
        $item = Item::create([
            'nama_barang' => 'Product A',
            'harga_barang' => 100000,
            'selling_price' => 150000,
            'kode_barang' => 'PRD-001',
            'category_id' => 1,
            'unit_id' => 1,
            'stok_barang' => 10
        ]);

        // Margin = (150k - 100k) / 150k = 50k / 150k = 33.33%
        $this->assertEquals(33.33333333333333, $item->profit_margin);
        // Potential = (150k - 100k) * 10 = 500k
        $this->assertEquals(500000, $item->profit_potential);
    }

    public function test_expiring_items_filter()
    {
        $item = Item::create([
            'nama_barang' => 'Medicine',
            'harga_barang' => 50000,
            'kode_barang' => 'MED-001',
            'category_id' => 1,
            'unit_id' => 1,
            'stok_barang' => 100
        ]);

        // Expiring in 10 days (Should be included)
        Batch::create([
            'item_id' => $item->id,
            'batch_number' => 'B1',
            'quantity' => 50,
            'expiry_date' => Carbon::now()->addDays(10)
        ]);

        // Expiring in 200 days (Should NOT be included in "soon" dashboard)
        Batch::create([
            'item_id' => $item->id,
            'batch_number' => 'B2',
            'quantity' => 50,
            'expiry_date' => Carbon::now()->addDays(200)
        ]);

        $response = $this->actingAs($this->admin)->get(route('reports.expiring'));
        $response->assertStatus(200);
        $response->assertSee('B1');
        $response->assertDontSee('B2');
    }
}
