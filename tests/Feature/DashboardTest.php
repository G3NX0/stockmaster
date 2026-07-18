<?php

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest cannot access dashboard', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

test('can access dashboard and see stats', function () {
    $user = User::factory()->create();
    Item::factory()->create(['stok_barang' => 10, 'harga_barang' => 1000]); // Value 10000

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertSee('Overview');
    $response->assertSee('10'); // Total Stock
    $response->assertSee('Rp 10.000'); // Total Value (formatted)
});
