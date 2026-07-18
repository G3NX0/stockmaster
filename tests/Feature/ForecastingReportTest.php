<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForecastingReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_forecasting_report()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $item = Item::factory()->create(['nama_barang' => 'Predictive Item', 'stok_barang' => 100]);
        
        // Create transactions to allow burn rate calculation
        Transaction::factory()->create([
            'item_id' => $item->id,
            'type' => 'out',
            'quantity' => 10,
            'created_at' => now()->subDays(5)
        ]);

        $response = $this->actingAs($admin)->get(route('reports.forecasting'));

        $response->assertStatus(200);
        $response->assertViewIs('reports.forecasting');
        $response->assertViewHas('items');
        $response->assertSee('AI Stock Forecasting');
    }

    public function test_staff_cannot_access_forecasting_report()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        
        $response = $this->actingAs($staff)->get(route('reports.forecasting'));

        $response->assertStatus(403);
    }
}
