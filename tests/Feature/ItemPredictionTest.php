<?php

use App\Models\Item;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;

test('item index includes predictions for all items', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $item = Item::factory()->create(['nama_barang' => 'Predictable Item', 'stok_barang' => 100]);
    
    // Create some transactions to allow prediction
    Transaction::factory()->create([
        'item_id' => $item->id,
        'type' => 'out',
        'quantity' => 10,
        'created_at' => now()->subDays(5)
    ]);

    $response = $this->actingAs($user)->get(route('items.index'));

    $response->assertStatus(200);
    $response->assertViewHas('items', function ($items) {
        return $items->first()->prediction !== null;
    });
});
