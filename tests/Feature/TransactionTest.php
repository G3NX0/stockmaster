<?php

use App\Models\Item;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('can create "in" transaction and increment stock', function () {
    $user = User::factory()->create();
    $item = Item::factory()->create(['stok_barang' => 10]);

    $response = $this->actingAs($user)->post(route('transactions.store'), [
        'item_id' => $item->id,
        'type' => 'in',
        'quantity' => 5,
        'note' => 'Restock'
    ]);

    $response->assertRedirect(route('transactions.index'));
    $this->assertDatabaseHas('transactions', [
        'item_id' => $item->id,
        'type' => 'in',
        'quantity' => 5
    ]);
    
    // Check if stock is updated
    $this->assertEquals(15, $item->fresh()->stok_barang);
});

test('can create "out" transaction and decrement stock', function () {
    $user = User::factory()->create();
    $item = Item::factory()->create(['stok_barang' => 10]);

    $response = $this->actingAs($user)->post(route('transactions.store'), [
        'item_id' => $item->id,
        'type' => 'out',
        'quantity' => 3,
        'note' => 'Sale'
    ]);

    $response->assertRedirect(route('transactions.index'));
    
    // Check if stock is updated
    $this->assertEquals(7, $item->fresh()->stok_barang);
});

test('cannot create "out" transaction if stock is insufficient', function () {
    $user = User::factory()->create();
    $item = Item::factory()->create(['stok_barang' => 2]);

    $response = $this->actingAs($user)->from(route('transactions.create'))->post(route('transactions.store'), [
        'item_id' => $item->id,
        'type' => 'out',
        'quantity' => 5,
        'note' => 'Oversell'
    ]);

    $response->assertRedirect(route('transactions.create'));
    $response->assertSessionHasErrors('quantity');
    
    // Stock should remain unchanged
    $this->assertEquals(2, $item->fresh()->stok_barang);
});
