<?php

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

use App\Models\User;

test('guest cannot access items', function () {
    $this->get(route('items.index'))->assertRedirect(route('login'));
});

test('can list items', function () {
    $user = User::factory()->create();
    Item::factory()->count(3)->create();

    $response = $this->actingAs($user)->get(route('items.index'));

    $response->assertStatus(200);
    $response->assertViewIs('items.index');
    $response->assertViewHas('items');
});

test('can create item', function () {
    $user = User::factory()->create();
    $itemData = [
        'nama_barang' => 'Test Item',
        'harga_barang' => 10000,
        'stok_barang' => 10,
        'kode_barang' => 'TEST-001',
        'min_stock' => 5,
    ];

    $response = $this->actingAs($user)->post(route('items.store'), $itemData);

    $response->assertRedirect(route('items.index'));
    $this->assertDatabaseHas('items', $itemData);
});

test('cannot create item with duplicate code', function () {
    $user = User::factory()->create();
    Item::create([
        'nama_barang' => 'Existing Item',
        'harga_barang' => 5000,
        'stok_barang' => 5,
        'kode_barang' => 'DUP-001',
        'min_stock' => 5,
    ]);

    $itemData = [
        'nama_barang' => 'New Item',
        'harga_barang' => 10000,
        'stok_barang' => 10,
        'kode_barang' => 'DUP-001',
        'min_stock' => 5,
    ];

    $response = $this->actingAs($user)->post(route('items.store'), $itemData);

    $response->assertSessionHasErrors('kode_barang');
});

test('can update item', function () {
    $user = User::factory()->create();
    $item = Item::factory()->create([
        'nama_barang' => 'Old Name',
        'min_stock' => 5,
    ]);

    $updatedData = [
        'nama_barang' => 'New Name',
        'harga_barang' => 7000,
        'stok_barang' => 7,
        'kode_barang' => 'NEW-001',
        'min_stock' => 5,
    ];

    $response = $this->actingAs($user)->put(route('items.update', $item->id), $updatedData);

    $response->assertRedirect(route('items.index'));
    $this->assertDatabaseHas('items', $updatedData);
});

test('can delete item', function () {
    $user = User::factory()->create();
    $item = Item::factory()->create();

    $response = $this->actingAs($user)->delete(route('items.destroy', $item->id));

    $response->assertRedirect(route('items.index'));
    $this->assertDatabaseMissing('items', ['id' => $item->id]);
});


