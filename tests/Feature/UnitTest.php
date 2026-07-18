<?php

use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest cannot access units', function () {
    $this->get(route('units.index'))->assertRedirect(route('login'));
});

test('can list units', function () {
    $user = User::factory()->create();
    Unit::create(['name' => 'Pieces', 'symbol' => 'pcs']);
    
    $response = $this->actingAs($user)->get(route('units.index'));

    $response->assertStatus(200);
    $response->assertSee('Pieces');
});

test('can create unit', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->post(route('units.store'), [
        'name' => 'Kilogram',
        'symbol' => 'kg'
    ]);

    $response->assertRedirect(route('units.index'));
    $this->assertDatabaseHas('units', ['name' => 'Kilogram', 'symbol' => 'kg']);
});

test('can update unit', function () {
    $user = User::factory()->create();
    $unit = Unit::create(['name' => 'Old Unit', 'symbol' => 'old']);

    $response = $this->actingAs($user)->put(route('units.update', $unit->id), [
        'name' => 'New Unit',
        'symbol' => 'new'
    ]);

    $response->assertRedirect(route('units.index'));
    $this->assertDatabaseHas('units', ['name' => 'New Unit']);
});

test('can delete unit', function () {
    $user = User::factory()->create();
    $unit = Unit::create(['name' => 'Delete Me', 'symbol' => 'del']);

    $response = $this->actingAs($user)->delete(route('units.destroy', $unit->id));

    $response->assertRedirect(route('units.index'));
    $this->assertDatabaseMissing('units', ['id' => $unit->id]);
});
