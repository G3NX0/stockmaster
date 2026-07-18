<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardZenithUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_has_zenith_ui_elements()
    {
        $user = User::factory()->create();
        
        // Create dummy data
        $cat = Category::create(['name' => 'Electronic']);
        Item::create([
            'nama_barang' => 'Zenith Processor',
            'kode_barang' => 'ZN-001',
            'category_id' => $cat->id,
            'stok_barang' => 2, // Critical stock for heartbeat test
            'min_stock' => 5,
            'harga_barang' => 5000000,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        
        // Verify Holographic Stats classes
        $response->assertSee('holographic-card');
        
        // Verify Heartbeat Pulse classes
        $response->assertSee('heartbeat-pulse');
        
        // Verify Liquid Progress Bar classes
        $response->assertSee('liquid-container');
        $response->assertSee('liquid-wave');
    }
}
