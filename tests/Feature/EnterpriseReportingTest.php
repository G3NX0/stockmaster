<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\Batch;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnterpriseReportingTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $staff;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->staff = User::factory()->create(['role' => 'staff']);
        
        // Setup basic data
        $category = Category::create(['name' => 'Electronics']);
        $unit = Unit::create(['name' => 'Pcs', 'symbol' => 'pcs']);
        
        Item::factory()->count(5)->create([
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'stok_barang' => 50,
            'harga_barang' => 100000,
            'selling_price' => 150000
        ]);
    }

    /** @test */
    public function admin_can_access_all_enterprise_reports()
    {
        $reports = [
            'reports.index',
            'reports.forecasting',
            'reports.heatmap',
            'reports.expiring',
            'reports.profit',
        ];

        foreach ($reports as $report) {
            $response = $this->actingAs($this->admin)->get(route($report));
            $response->assertStatus(200);
        }
    }

    /** @test */
    public function staff_is_denied_access_to_enterprise_reports()
    {
        $reports = [
            'reports.index',
            'reports.forecasting',
            'reports.heatmap',
            'reports.expiring',
            'reports.profit',
        ];

        foreach ($reports as $report) {
            $response = $this->actingAs($this->staff)->get(route($report));
            $response->assertStatus(403);
        }
    }

    /** @test */
    public function forecasting_report_calculates_burn_rate_correctly()
    {
        $item = Item::first();
        
        // Create transactions to simulate burn rate
        // 30 items out in 30 days = 1 item/day burn rate
        Transaction::create([
            'item_id' => $item->id,
            'type' => 'out',
            'quantity' => 30,
            'user_id' => $this->admin->id,
            'created_at' => now()->subDays(15)
        ]);

        $response = $this->actingAs($this->admin)->get(route('reports.forecasting'));
        
        $response->assertStatus(200);
        // With 50 stock and 1/day burn rate, days left should be around 50
        $response->assertSee('50 DAYS');
    }

    /** @test */
    public function expiring_report_shows_items_near_expiry()
    {
        $item = Item::first();
        Batch::create([
            'item_id' => $item->id,
            'batch_number' => 'BATCH-001',
            'quantity' => 10,
            'expiry_date' => now()->addDays(10)
        ]);

        $response = $this->actingAs($this->admin)->get(route('reports.expiring'));
        
        $response->assertStatus(200);
        $response->assertSee('BATCH-001');
        $response->assertSee($item->nama_barang);
    }
    /** @test */
    public function heatmap_report_handles_items_without_unit()
    {
        $category = Category::first();
        // Create an item without a unit_id
        Item::factory()->create([
            'category_id' => $category->id,
            'unit_id' => null,
            'nama_barang' => 'Unitless Item'
        ]);

        $response = $this->actingAs($this->admin)->get(route('reports.heatmap'));
        
        $response->assertStatus(200);
        $response->assertSee('Unitless Item');
        $response->assertSee('Unit'); // Fallback name
    }
}
