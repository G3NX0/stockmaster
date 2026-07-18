<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Reconciliation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReconciliationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_create_reconciliation_and_update_stock()
    {
        $item = Item::factory()->create([
            'stok_barang' => 100,
            'nama_barang' => 'Test Item'
        ]);

        $response = $this->actingAs($this->admin)->post(route('reconciliations.store'), [
            'item_id' => $item->id,
            'physical_stock' => 95,
            'reason' => 'Damaged goods found during audit'
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('reconciliations', [
            'item_id' => $item->id,
            'physical_stock' => 95,
            'system_stock' => 100,
            'difference' => -5
        ]);

        $this->assertEquals(95, $item->refresh()->stok_barang);
    }

    public function test_reconciliation_is_logged_in_activity_log()
    {
        $item = Item::factory()->create(['stok_barang' => 50]);

        $this->actingAs($this->admin)->post(route('reconciliations.store'), [
            'item_id' => $item->id,
            'physical_stock' => 60,
            'reason' => 'Inventory count correction'
        ]);

        $this->assertDatabaseHas('activity_log', [
            'description' => 'created',
            'subject_type' => Reconciliation::class
        ]);
    }
}
