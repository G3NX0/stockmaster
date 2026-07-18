<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Transaction;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->admin()->create();
    }

    public function test_guest_cannot_access_reports()
    {
        $response = $this->get(route('reports.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_can_access_reports_and_see_data()
    {
        $category = Category::factory()->create(['name' => 'Electronic']);
        $unit = Unit::factory()->create(['name' => 'Pcs']);
        $supplier = Supplier::factory()->create(['name' => 'Global Tech']);
        
        $item = Item::factory()->create([
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'supplier_id' => $supplier->id,
            'stok_barang' => 10
        ]);

        // Create some transactions
        Transaction::factory()->create(['item_id' => $item->id, 'type' => 'in', 'quantity' => 20]);
        Transaction::factory()->create(['item_id' => $item->id, 'type' => 'out', 'quantity' => 5]);

        $response = $this->actingAs($this->user)->get(route('reports.index'));
        
        $response->assertStatus(200);
        $response->assertSee('Electronic');
        $response->assertSee('5 UNIT'); // Total out
    }
}
