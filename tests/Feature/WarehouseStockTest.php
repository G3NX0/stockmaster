<?php

use App\Models\Item;
use App\Models\Warehouse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('item can have stock in different warehouses', function () {
    $item = Item::factory()->create();
    $w1 = Warehouse::create(['name' => 'Gudang A']);
    $w2 = Warehouse::create(['name' => 'Gudang B']);

    $item->warehouses()->attach($w1->id, ['stock' => 10]);
    $item->warehouses()->attach($w2->id, ['stock' => 5]);

    expect($item->warehouses()->find($w1->id)->pivot->stock)->toBe(10);
    expect($item->warehouses()->find($w2->id)->pivot->stock)->toBe(5);
});
