<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Contracts;
use App\Models\Access_Point;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function authenticated_user_can_list_clients(): void
    {
        Client::factory()->count(3)->create();

        $response = $this->actingAs($this->user)
            ->get(route('clients'));

        $response->assertStatus(200);
    }

    /** @test */
    public function unauthenticated_user_redirected_from_clients(): void
    {
        $response = $this->get(route('clients'));
        $response->assertRedirect('/login');
    }

    /** @test */
    public function authenticated_user_can_create_client_with_valid_data(): void
    {
        $contract = Contracts::factory()->create();
        $accessPoint = Access_Point::factory()->create();

        $response = $this->actingAs($this->user)
            ->post(route('clients.store'), [
                'first_name' => 'Juan',
                'last_name' => 'Pérez',
                'ip_address' => '192.168.1.100',
                'phone' => '1234567890',
                'contracts_id' => $contract->id,
                'access_point_id' => $accessPoint->id,
                'street_address' => 'Calle 123',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('cliente', [
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'ip' => '192.168.1.100',
        ]);
    }

    /** @test */
    public function client_creation_requires_valid_ip(): void
    {
        $contract = Contracts::factory()->create();
        $accessPoint = Access_Point::factory()->create();

        $response = $this->actingAs($this->user)
            ->post(route('clients.store'), [
                'first_name' => 'Juan',
                'last_name' => 'Pérez',
                'ip_address' => 'invalid-ip',
                'phone' => '1234567890',
                'contracts_id' => $contract->id,
                'access_point_id' => $accessPoint->id,
                'street_address' => 'Calle 123',
            ]);

        $response->assertSessionHasErrors('ip_address');
    }

    /** @test */
    public function client_creation_requires_existing_contract(): void
    {
        $accessPoint = Access_Point::factory()->create();

        $response = $this->actingAs($this->user)
            ->post(route('clients.store'), [
                'first_name' => 'Juan',
                'last_name' => 'Pérez',
                'ip_address' => '192.168.1.100',
                'phone' => '1234567890',
                'contracts_id' => 9999,
                'access_point_id' => $accessPoint->id,
                'street_address' => 'Calle 123',
            ]);

        $response->assertSessionHasErrors('contracts_id');
    }

    /** @test */
    public function authenticated_user_can_view_client_details(): void
    {
        $client = Client::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('clients.show', $client->id));

        $response->assertStatus(200);
    }

    /** @test */
    public function authenticated_user_can_edit_client(): void
    {
        $client = Client::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('clients.edit', $client->id));

        $response->assertStatus(200);
    }

    /** @test */
    public function authenticated_user_can_update_client(): void
    {
        $client = Client::factory()->create();

        $response = $this->actingAs($this->user)
            ->put(route('clients.update', $client->id), [
                'first_name' => 'Nombre Actualizado',
                'last_name' => 'Apellido Actualizado',
                'ip_address' => '192.168.1.200',
                'phone' => '1234567890',
                'contracts_id' => $client->id_plan,
                'access_point_id' => $client->id_point,
                'street_address' => 'Nueva direccion',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('cliente', [
            'id' => $client->id,
            'nombre' => 'Nombre Actualizado',
        ]);
    }

    /** @test */
    public function authenticated_user_can_delete_client(): void
    {
        $client = Client::factory()->create();

        $response = $this->actingAs($this->user)
            ->delete(route('clients.destroy', $client->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('cliente', [
            'id' => $client->id,
        ]);
    }

    /** @test */
    public function client_can_be_banned(): void
    {
        $client = Client::factory()->create(['is_banned' => false]);

        $this->assertFalse($client->is_banned);

        $client->update(['is_banned' => true]);

        $this->assertTrue($client->fresh()->is_banned);
    }

    /** @test */
    public function client_belongs_to_contract(): void
    {
        $contract = Contracts::factory()->create();
        $client = Client::factory()->create(['id_plan' => $contract->id]);

        $this->assertInstanceOf(Contracts::class, $client->contract);
        $this->assertEquals($contract->id, $client->contract->id);
    }

    /** @test */
    public function client_belongs_to_access_point(): void
    {
        $accessPoint = Access_Point::factory()->create();
        $client = Client::factory()->create(['id_point' => $accessPoint->id]);

        $this->assertInstanceOf(Access_Point::class, $client->accessPoint);
        $this->assertEquals($accessPoint->id, $client->accessPoint->id);
    }

    /** @test */
    public function client_has_many_payments(): void
    {
        $client = Client::factory()->create();
        \App\Models\Payments::factory()->count(3)->create(['id_cliente' => $client->id]);

        $this->assertCount(3, $client->pagos);
    }
}
