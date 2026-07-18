<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    /** @test */
    public function guest_cannot_access_suppliers()
    {
        $response = $this->get(route('suppliers.index'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function admin_can_list_suppliers()
    {
        Supplier::factory()->count(3)->create();
        
        $response = $this->actingAs($this->admin)->get(route('suppliers.index'));
        $response->assertStatus(200);
        $response->assertSee(Supplier::first()->name);
    }

    /** @test */
    public function admin_can_create_supplier()
    {
        $response = $this->actingAs($this->admin)->post(route('suppliers.store'), [
            'name' => 'Zenith Corp',
            'contact_person' => 'John Doe',
            'phone' => '08123456789',
            'email' => 'zenith@master.com',
            'address' => 'Neural Hub 1',
        ]);

        $response->assertRedirect(route('suppliers.index'));
        $this->assertDatabaseHas('suppliers', ['name' => 'Zenith Corp']);
    }

    /** @test */
    public function admin_can_update_supplier()
    {
        $supplier = Supplier::factory()->create();
        
        $response = $this->actingAs($this->admin)->put(route('suppliers.update', $supplier), [
            'name' => 'Updated Supplier',
            'contact_person' => 'Jane Doe',
            'phone' => '0999999',
            'address' => 'Updated Address'
        ]);

        $response->assertRedirect(route('suppliers.index'));
        $this->assertDatabaseHas('suppliers', ['name' => 'Updated Supplier']);
    }

    /** @test */
    public function admin_can_delete_supplier()
    {
        $supplier = Supplier::factory()->create();
        
        $response = $this->actingAs($this->admin)->delete(route('suppliers.destroy', $supplier));
        
        $response->assertRedirect(route('suppliers.index'));
        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
    }
}
