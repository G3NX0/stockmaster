<?php

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest cannot access categories', function () {
    $this->get(route('categories.index'))->assertRedirect(route('login'));
});

test('can list categories', function () {
    $user = User::factory()->create();
    Category::create(['name' => 'Electronic']);
    
    $response = $this->actingAs($user)->get(route('categories.index'));

    $response->assertStatus(200);
    $response->assertSee('Electronic');
});

test('can create category', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->post(route('categories.store'), [
        'name' => 'Food',
        'description' => 'Daily food items'
    ]);

    $response->assertRedirect(route('categories.index'));
    $this->assertDatabaseHas('categories', ['name' => 'Food']);
});

test('can update category', function () {
    $user = User::factory()->create();
    $category = Category::create(['name' => 'Old Name']);

    $response = $this->actingAs($user)->put(route('categories.update', $category->id), [
        'name' => 'New Name'
    ]);

    $response->assertRedirect(route('categories.index'));
    $this->assertDatabaseHas('categories', ['name' => 'New Name']);
});

test('can delete category', function () {
    $user = User::factory()->create();
    $category = Category::create(['name' => 'Delete Me']);

    $response = $this->actingAs($user)->delete(route('categories.destroy', $category->id));

    $response->assertRedirect(route('categories.index'));
    $this->assertDatabaseMissing('categories', ['id' => $category->id]);
});
