<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageAccessibilityTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    /** @test */
    public function all_core_pages_are_accessible_by_admin()
    {
        $routes = [
            'dashboard',
            'scanner',
            'guide',
            'items.index',
            'categories.index',
            'units.index',
            'suppliers.index',
            'transactions.index',
            'reports.index',
            'reports.profit',
            'reports.forecasting',
            'reports.heatmap',
            'reports.expiring',
            'assets.index',
            'customers.index',
            'transfers.index',
            'pos.index',
            'reconciliations.index',
            'logs.index',
            'users.index',
            'backups.index',
            'profile.edit',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($this->admin)->get(route($route));
            $response->assertStatus(200, "Route [{$route}] is not accessible!");
        }
    }

    /** @test */
    public function guest_is_redirected_to_login()
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }
}
