<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LabelPrintTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_generate_item_label_pdf()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $item = Item::factory()->create([
            'nama_barang' => 'Label Item',
            'kode_barang' => 'LBL-001'
        ]);

        $response = $this->actingAs($admin)->get(route('items.print-label', $item->id));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}
