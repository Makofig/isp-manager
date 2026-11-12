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
            ->post('/access-point/store', [
                'ssid' => 'AP Central',
                'frequency' => '2.4GHz',
                'ip_address' => '192.168.1.1',
                'location' => 'Oficina Central',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseCount('accespoint', 1);
        $this->assertDatabaseHas('accespoint', ['ssid' => 'AP Central']);
    }

    public function test_create_contract(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/contracts/store', [
                'name' => 'Plan Basico',
                'megabytes' => 100,
                'price' => 25000,
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
            'ssid' => 'AP Test',
            'frecuencia' => '5GHz',
            'ip_ap' => '192.168.1.10',
            'localidad' => 'Sucursal',
        ]);

        $response = $this->actingAs($this->user)
            ->post('/clients/store', [
                'first_name' => 'Juan',
                'last_name' => 'Pérez',
                'ip_address' => '10.0.0.1',
                'phone' => '555-1234',
                'contracts_id' => $contract->id,
                'access_point_id' => $accessPoint->id,
                'street_address' => 'Calle 123',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseCount('cliente', 1);
        $this->assertDatabaseHas('cliente', ['nombre' => 'Juan', 'apellido' => 'Pérez']);
    }

    public function test_generate_quota(): void
    {
        $contract = Contracts::create(['nombre' => 'Plan Q', 'megabytes' => 50, 'costo' => 30000]);
        $ap = Access_Point::create(['ssid' => 'AP Q', 'frecuencia' => '5GHz', 'ip_ap' => '192.168.1.20', 'localidad' => 'Sucursal Q']);
        Client::create([
            'id_plan' => $contract->id,
            'id_point' => $ap->id,
            'nombre' => 'Maria',
            'apellido' => 'Garcia',
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
        $ap = Access_Point::create(['ssid' => 'AP P', 'frecuencia' => '5GHz', 'ip_ap' => '192.168.1.30', 'localidad' => 'Sucursal P']);
        $client = Client::create([
            'id_plan' => $contract->id,
            'id_point' => $ap->id,
            'nombre' => 'Carlos',
            'apellido' => 'Lopez',
            'direccion' => 'Calle 789',
            'telefono' => '555-9012',
            'ip' => '10.0.0.3',
        ]);

        $quota = Quota::create(['numero' => now()->month, 'year' => now()->year]);

        $payment = Payments::create([
            'id_cliente' => $client->id,
            'id_cuota' => $quota->id,
            'costo' => $contract->costo,
            'abonado' => 0,
        ]);

        $response = $this->actingAs($this->user)
            ->put("/payments/update/{$payment->id}", [
                'amount' => 15000,
                'coment' => 'Pago completo',
                'payment_date' => now()->toDateString(),
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pagos', ['id' => $payment->id, 'abonado' => 15000]);
    }
}
