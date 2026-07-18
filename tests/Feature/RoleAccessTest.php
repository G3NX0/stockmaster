<?php
namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_reports()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/reports');

        $response->assertStatus(200);
    }

    public function test_staff_cannot_access_reports()
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $response = $this->actingAs($staff)->get('/reports');

        $response->assertStatus(403);
    }

    public function test_staff_cannot_access_activity_logs()
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $response = $this->actingAs($staff)->get('/activity-logs');

        $response->assertStatus(403);
    }

    public function test_staff_can_access_dashboard()
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $response = $this->actingAs($staff)->get('/');

        $response->assertStatus(200);
    }
}
