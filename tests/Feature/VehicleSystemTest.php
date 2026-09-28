<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Setting;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $driver;
    protected Vehicle $vehicle;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name'     => 'بەڕێوەبەر',
            'email'    => 'admin@test.com',
            'password' => bcrypt('password123'),
            'role'     => 'admin',
            'active'   => true,
        ]);

        $this->driver = User::create([
            'name'     => 'شۆفێر',
            'email'    => 'driver@test.com',
            'password' => bcrypt('password123'),
            'role'     => 'driver',
            'active'   => true,
        ]);

        $this->vehicle = Vehicle::create([
            'number' => 'SUL-5000',
            'type'   => 'تۆیۆتا',
            'model'  => 'Corolla',
            'active' => true,
            'status' => 'available',
        ]);
    }

    public function test_login_flow()
    {
        $response = $this->post('/login', [
            'email'    => 'admin@test.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($this->admin);

        // Check login audit log
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->admin->id,
            'action'  => 'user_login',
        ]);
    }

    public function test_driver_cannot_access_admin_routes()
    {
        $this->actingAs($this->driver);

        $response = $this->get('/drivers');
        $response->assertStatus(403);

        $response = $this->get('/vehicles');
        $response->assertStatus(403);

        $response = $this->get('/reports');
        $response->assertStatus(403);

        $response = $this->get('/backups');
        $response->assertStatus(403);

        $response = $this->get('/settings');
        $response->assertStatus(403);

        $response = $this->get('/audit-logs');
        $response->assertStatus(403);
    }

    public function test_admin_has_full_access()
    {
        $this->actingAs($this->admin);

        $this->get('/dashboard')->assertStatus(200);
        $this->get('/movements')->assertStatus(200);
        $this->get('/drivers')->assertStatus(200);
        $this->get('/vehicles')->assertStatus(200);
        $this->get('/reports')->assertStatus(200);
        $this->get('/backups')->assertStatus(200);
        $this->get('/audit-logs')->assertStatus(200);
        $this->get('/settings')->assertStatus(200);
    }

    public function test_departure_and_return_cycle()
    {
        $this->actingAs($this->driver);

        // Record departure
        $response = $this->post('/driver/departure', [
            'vehicle_id'  => $this->vehicle->id,
            'destination' => 'سلێمانی - ناوەندی شار',
            'purpose'     => 'گەیاندنی بەڵگەنامەکان',
        ]);

        $response->assertRedirect('/driver/active');
        $this->assertDatabaseHas('vehicle_movements', [
            'vehicle_id'  => $this->vehicle->id,
            'user_id'     => $this->driver->id,
            'destination' => 'سلێمانی - ناوەندی شار',
            'status'      => 'out',
        ]);

        $movement = VehicleMovement::first();

        // Driver cannot start another trip while out
        $response2 = $this->post('/driver/departure', [
            'vehicle_id'  => $this->vehicle->id,
            'destination' => 'هەولێر',
        ]);
        $response2->assertSessionHas('error');

        // Record Return
        $returnResponse = $this->post("/driver/return/{$movement->id}");
        $returnResponse->assertRedirect('/driver/home');

        $movement->refresh();
        $this->assertEquals('returned', $movement->status);
        $this->assertNotNull($movement->return_time);
        $this->assertNotNull($movement->duration_minutes);

        // Check return audit log
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->driver->id,
            'action'  => 'return_recorded',
        ]);
    }

    public function test_admin_override()
    {
        $this->actingAs($this->admin);

        // Create initial open trip
        VehicleMovement::create([
            'vehicle_id'     => $this->vehicle->id,
            'user_id'        => $this->driver->id,
            'destination'    => 'گەشتی یەکەم',
            'departure_time' => now(),
            'status'         => 'out',
        ]);

        // Admin overrides to send same driver/vehicle
        $response = $this->post('/driver/departure', [
            'vehicle_id'  => $this->vehicle->id,
            'user_id'     => $this->driver->id,
            'destination' => 'گەشتی دووەم بە تێپەڕاندن',
            'override'    => '1',
        ]);

        $response->assertRedirect('/movements');

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->admin->id,
            'action'  => 'departure_overridden',
        ]);
    }

    public function test_settings_update()
    {
        $this->actingAs($this->admin);

        $response = $this->post('/settings', [
            'org_name'        => 'سیستەمی نوێکراوە',
            'attention_hours' => '3',
            'warning_hours'   => '6',
            'dark_mode'       => '1',
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals('سیستەمی نوێکراوە', Setting::get('org_name'));
        $this->assertEquals('3', Setting::get('attention_hours'));
        $this->assertEquals('6', Setting::get('warning_hours'));
    }
}
