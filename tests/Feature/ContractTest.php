<?php

namespace Tests\Feature;

use App\Models\Contracts;
use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContractTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function authenticated_user_can_list_contracts(): void
    {
        Contracts::factory()->count(3)->create();

        $response = $this->actingAs($this->user)
            ->get(route('contracts'));

        $response->assertStatus(200);
    }

    /** @test */
    public function authenticated_user_can_create_contract(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('contracts.store'), [
                'nombre' => 'Plan Premium',
                'megabytes' => 100,
                'costo' => 299.99,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('plan', [
            'nombre' => 'Plan Premium',
            'megabytes' => 100,
        ]);
    }

    /** @test */
    public function contract_creation_requires_name(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('contracts.store'), [
                'megabytes' => 100,
                'costo' => 299.99,
            ]);

        $response->assertSessionHasErrors('nombre');
    }

    /** @test */
    public function contract_creation_requires_minimum_1_megabyte(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('contracts.store'), [
                'nombre' => 'Plan Test',
                'megabytes' => 0,
                'costo' => 100,
            ]);

        $response->assertSessionHasErrors('megabytes');
    }

    /** @test */
    public function contract_creation_requires_non_negative_price(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('contracts.store'), [
                'nombre' => 'Plan Test',
                'megabytes' => 10,
                'costo' => -50,
            ]);

        $response->assertSessionHasErrors('costo');
    }

    /** @test */
    public function authenticated_user_can_edit_contract(): void
    {
        $contract = Contracts::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('contracts.edit', $contract->id));

        $response->assertStatus(200);
    }

    /** @test */
    public function authenticated_user_can_update_contract(): void
    {
        $contract = Contracts::factory()->create();

        $response = $this->actingAs($this->user)
            ->put(route('contracts.update', $contract->id), [
                'nombre' => 'Plan Actualizado',
                'megabytes' => 200,
                'costo' => 399.99,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('plan', [
            'id' => $contract->id,
            'nombre' => 'Plan Actualizado',
            'megabytes' => 200,
        ]);
    }

    /** @test */
    public function contract_with_clients_cannot_be_deleted(): void
    {
        $contract = Contracts::factory()->create();
        Client::factory()->create(['id_plan' => $contract->id]);

        $response = $this->actingAs($this->user)
            ->delete(route('contracts.destroy', $contract->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('plan', ['id' => $contract->id]);
    }

    /** @test */
    public function contract_without_clients_can_be_deleted(): void
    {
        $contract = Contracts::factory()->create();

        $response = $this->actingAs($this->user)
            ->delete(route('contracts.destroy', $contract->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('plan', ['id' => $contract->id]);
    }

    /** @test */
    public function contract_has_many_clients(): void
    {
        $contract = Contracts::factory()->create();
        Client::factory()->count(3)->create(['id_plan' => $contract->id]);

        $this->assertCount(3, $contract->clients);
    }
}
