<?php

namespace Tests\Feature;

use App\Models\Access_Point;
use App\Models\Client;
use App\Models\Contracts;
use App\Models\Payments;
use App\Models\Quota;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BasicFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_create_access_point(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/access_points', [
                'nombre' => 'AP Central',
                'ip' => '192.168.1.1',
                'modelo' => 'Cisco',
                'ubicacion' => 'Oficina Central',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseCount('access_point', 1);
        $this->assertDatabaseHas('access_point', ['nombre' => 'AP Central']);
    }

    public function test_create_contract(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/contracts', [
                'nombre' => 'Plan Basico',
                'megabytes' => 100,
                'costo' => 25000,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseCount('plan', 1);
        $this->assertDatabaseHas('plan', ['nombre' => 'Plan Basico']);
    }

    public function test_create_client(): void
    {
        $contract = Contracts::create([
            'nombre' => 'Plan Test',
            'megabytes' => 50,
            'costo' => 20000,
        ]);

        $accessPoint = Access_Point::create([
            'nombre' => 'AP Test',
            'ip' => '192.168.1.10',
            'modelo' => 'TP-Link',
            'ubicacion' => 'Sucursal',
        ]);

        $response = $this->actingAs($this->user)
            ->post('/clients', [
                'id_plan' => $contract->id,
                'id_point' => $accessPoint->id,
                'nombre' => 'Juan',
                'apellido' => 'Pérez',
                'email' => 'juan@test.com',
                'direccion' => 'Calle 123',
                'telefono' => '555-1234',
                'ip' => '10.0.0.1',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseCount('cliente', 1);
        $this->assertDatabaseHas('cliente', ['nombre' => 'Juan', 'apellido' => 'Pérez']);
    }

    public function test_generate_quota(): void
    {
        Contracts::create(['nombre' => 'Plan Q', 'megabytes' => 50, 'costo' => 30000]);
        Access_Point::create(['nombre' => 'AP Q', 'ip' => '192.168.1.20', 'modelo' => 'TP-Link', 'ubicacion' => 'Sucursal Q']);
        Client::create([
            'id_plan' => 1,
            'id_point' => 1,
            'nombre' => 'Maria',
            'apellido' => 'Garcia',
            'email' => 'maria@test.com',
            'direccion' => 'Calle 456',
            'telefono' => '555-5678',
            'ip' => '10.0.0.2',
        ]);

        $this->actingAs($this->user)
            ->artisan('app:generate-monthly-quotas')
            ->assertSuccessful();

        $this->assertDatabaseCount('cuotas', 1);
        $this->assertDatabaseHas('cuotas', ['numero' => now()->month, 'year' => now()->year]);
    }

    public function test_pay_quota(): void
    {
        $contract = Contracts::create(['nombre' => 'Plan P', 'megabytes' => 50, 'costo' => 15000]);
        $ap = Access_Point::create(['nombre' => 'AP P', 'ip' => '192.168.1.30', 'modelo' => 'Cisco', 'ubicacion' => 'Sucursal P']);
        $client = Client::create([
            'id_plan' => $contract->id,
            'id_point' => $ap->id,
            'nombre' => 'Carlos',
            'apellido' => 'Lopez',
            'email' => 'carlos@test.com',
            'direccion' => 'Calle 789',
            'telefono' => '555-9012',
            'ip' => '10.0.0.3',
        ]);

        $quota = Quota::create(['numero' => now()->month, 'year' => now()->year]);

        Payments::create([
            'id_cliente' => $client->id,
            'id_cuota' => $quota->id,
            'costo' => $contract->costo,
            'abonado' => 0,
        ]);

        $response = $this->actingAs($this->user)
            ->put("/payments/1", [
                'abonado' => 15000,
                'fecha_pago' => now()->toDateString(),
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pagos', ['id' => 1, 'abonado' => 15000]);
    }
}
