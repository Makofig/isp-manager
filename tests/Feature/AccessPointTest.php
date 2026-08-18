<?php

namespace Tests\Feature;

use App\Models\Access_Point;
use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessPointTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function authenticated_user_can_list_access_points(): void
    {
        Access_Point::factory()->count(3)->create();

        $response = $this->actingAs($this->user)
            ->get(route('access-point'));

        $response->assertStatus(200);
    }

    /** @test */
    public function authenticated_user_can_create_access_point(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('access-point.store'), [
                'ssid' => 'MiRed_WiFi',
                'frequency' => '2.4GHz',
                'ip_address' => '192.168.0.1',
                'location' => 'Centro',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('accespoint', [
            'ssid' => 'MiRed_WiFi',
            'frecuencia' => '2.4GHz',
        ]);
    }

    /** @test */
    public function access_point_creation_requires_ssid(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('access-point.store'), [
                'frequency' => '2.4GHz',
                'ip_address' => '192.168.0.1',
                'location' => 'Centro',
            ]);

        $response->assertSessionHasErrors('ssid');
    }

    /** @test */
    public function access_point_creation_requires_valid_ip(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('access-point.store'), [
                'ssid' => 'Test',
                'frequency' => '2.4GHz',
                'ip_address' => 'invalid-ip',
                'location' => 'Centro',
            ]);

        $response->assertSessionHasErrors('ip_address');
    }

    /** @test */
    public function authenticated_user_can_view_access_point_details(): void
    {
        $accessPoint = Access_Point::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('access-point.show', $accessPoint->id));

        $response->assertStatus(200);
    }

    /** @test */
    public function authenticated_user_can_edit_access_point(): void
    {
        $accessPoint = Access_Point::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('access-point.edit', $accessPoint->id));

        $response->assertStatus(200);
    }

    /** @test */
    public function authenticated_user_can_update_access_point(): void
    {
        $accessPoint = Access_Point::factory()->create();

        $response = $this->actingAs($this->user)
            ->put(route('access-point.update', $accessPoint->id), [
                'ssid' => 'Red Actualizada',
                'frequency' => '5GHz',
                'ip_address' => '10.0.0.1',
                'location' => 'Norte',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('accespoint', [
            'id' => $accessPoint->id,
            'ssid' => 'Red Actualizada',
        ]);
    }

    /** @test */
    public function access_point_with_clients_cannot_be_deleted(): void
    {
        $accessPoint = Access_Point::factory()->create();
        Client::factory()->create(['id_point' => $accessPoint->id]);

        $response = $this->actingAs($this->user)
            ->delete(route('access-point.destroy', $accessPoint->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('accespoint', ['id' => $accessPoint->id]);
    }

    /** @test */
    public function access_point_without_clients_can_be_deleted(): void
    {
        $accessPoint = Access_Point::factory()->create();

        $response = $this->actingAs($this->user)
            ->delete(route('access-point.destroy', $accessPoint->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('accespoint', ['id' => $accessPoint->id]);
    }

    /** @test */
    public function access_point_shows_client_count(): void
    {
        $accessPoint = Access_Point::factory()->create();
        Client::factory()->count(5)->create(['id_point' => $accessPoint->id]);

        $this->assertEquals(5, $accessPoint->clients->count());
    }
}
