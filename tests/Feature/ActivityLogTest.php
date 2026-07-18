<?php

namespace Tests\Feature;

use App\Models\User;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    /** @test */
    public function guest_cannot_access_logs()
    {
        $response = $this->get(route('logs.index'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function admin_can_view_activity_logs()
    {
        activity()->log('Zenith Protocol Initialized');
        
        $response = $this->actingAs($this->admin)->get(route('logs.index'));
        
        $response->assertStatus(200);
        $response->assertSee('Zenith Protocol Initialized');
    }

    /** @test */
    public function logs_are_paginated()
    {
        for ($i = 0; $i < 25; $i++) {
            activity()->log("Log entry $i");
        }

        $response = $this->actingAs($this->admin)->get(route('logs.index'));
        $response->assertStatus(200);
        
        // Check if pagination is working (default 20 per page)
        $this->assertCount(20, $response->viewData('logs'));
    }
}
