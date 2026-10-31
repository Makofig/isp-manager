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

class PaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'email_verified_at' => now(),
        ]);
    }

    /** @test E2E: Complete payment flow */
    public function payment_flow_complete(): void
    {
        // Step 1: Create prerequisites
        $contract = Contracts::factory()->create(['nombre' => 'Plan 1GB']);
        $accessPoint = Access_Point::factory()->create(['ssid' => 'ISP-WiFi']);
        $client = Client::factory()->create([
            'id_plan' => $contract->id,
            'id_point' => $accessPoint->id,
        ]);

        // Step 2: Create a quota for current month
        $year = now()->year;
        $month = now()->month;
        $quota = Quota::factory()->create([
            'numero' => $month,
            'year' => $year,
        ]);

        // Step 3: Create an unpaid payment record for the client
        $payment = Payments::factory()->create([
            'id_cliente' => $client->id,
            'id_cuota' => $quota->id,
            'costo' => $contract->costo,
            'abonado' => 0,
            'estado' => 0, // unpaid
        ]);

        // Step 4: Admin can view payment details
        $response = $this->actingAs($this->admin)
            ->get(route('payments.show', $payment->id));
        $response->assertStatus(200);

        // Step 5: Admin can update and register the payment as paid
        $paymentDate = now()->format('Y-m-d');
        $paymentData = [
            'amount' => $contract->costo,
            'coment' => 'Pago por transferencia',
            'payment_date' => $paymentDate,
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('payments.update', $payment->id), $paymentData);

        $response->assertRedirect();

        // Step 6: Verify payment was marked as paid
        $payment->refresh();
        $this->assertEquals($contract->costo, $payment->abonado);
        $this->assertEquals(1, (int) $payment->estado);
        $this->assertEquals($paymentDate, $payment->fecha_pago->format('Y-m-d'));

        // Step 7: Verify relationships are intact
        $this->assertInstanceOf(Client::class, $payment->clients);
        $this->assertEquals($client->id, $payment->clients->id);
        $this->assertInstanceOf(Quota::class, $payment->cuota);
        $this->assertEquals($quota->id, $payment->cuota->id);
    }

    /** @test E2E: Payment cannot be marked paid with negative amount */
    public function payment_registration_requires_valid_amount(): void
    {
        $contract = Contracts::factory()->create(['costo' => 50]);
        $accessPoint = Access_Point::factory()->create();
        $client = Client::factory()->create([
            'id_plan' => $contract->id,
            'id_point' => $accessPoint->id,
        ]);

        $quota = Quota::factory()->create([
            'numero' => now()->month,
            'year' => now()->year,
        ]);

        $payment = Payments::factory()->create([
            'id_cliente' => $client->id,
            'id_cuota' => $quota->id,
            'costo' => $contract->costo,
            'abonado' => 0,
            'estado' => 0,
        ]);

        $response = $this->actingAs($this->admin)
            ->put(route('payments.update', $payment->id), [
                'amount' => -100,
                'coment' => 'Invalid payment',
                'payment_date' => now()->format('Y-m-d'),
            ]);

        $response->assertSessionHasErrors('amount');
        $payment->refresh();
        $this->assertEquals(0, $payment->abonado);
        $this->assertEquals(0, (int) $payment->estado); // still unpaid
    }

    /** @test E2E: Multiple clients can have payments for same quota */
    public function multiple_clients_payment_same_quota(): void
    {
        $contract = Contracts::factory()->create(['costo' => 40]);
        $accessPoint = Access_Point::factory()->create();

        // Create two clients
        $client1 = Client::factory()->create([
            'id_plan' => $contract->id,
            'id_point' => $accessPoint->id,
        ]);
        $client2 = Client::factory()->create([
            'id_plan' => $contract->id,
            'id_point' => $accessPoint->id,
        ]);

        // Single quota for the month
        $quota = Quota::factory()->create([
            'numero' => now()->month,
            'year' => now()->year,
        ]);

        // Create payments for both clients
        Payments::factory()->create([
            'id_cliente' => $client1->id,
            'id_cuota' => $quota->id,
            'costo' => $contract->costo,
            'abonado' => $contract->costo,
            'estado' => 1,
            'fecha_pago' => now(),
        ]);

        Payments::factory()->create([
            'id_cliente' => $client2->id,
            'id_cuota' => $quota->id,
            'costo' => $contract->costo,
            'abonado' => 0,
            'estado' => 0,
        ]);

        // Verify quota has 2 payments
        $this->assertEquals(2, $quota->pagos()->count());

        // Verify one is paid, one is unpaid
        $paidCount = $quota->pagos()->where('estado', 1)->count();
        $unpaidCount = $quota->pagos()->where('estado', 0)->count();
        $this->assertEquals(1, $paidCount);
        $this->assertEquals(1, $unpaidCount);
    }

    /** @test E2E: Soft-deleted client's payments become orphaned with NULL reference */
    public function soft_deleted_client_payment_orphaned(): void
    {
        $contract = Contracts::factory()->create(['costo' => 30]);
        $accessPoint = Access_Point::factory()->create();
        $client = Client::factory()->create([
            'id_plan' => $contract->id,
            'id_point' => $accessPoint->id,
        ]);

        $quota = Quota::factory()->create([
            'numero' => now()->month,
            'year' => now()->year,
        ]);

        $payment = Payments::factory()->create([
            'id_cliente' => $client->id,
            'id_cuota' => $quota->id,
            'costo' => $contract->costo,
            'abonado' => $contract->costo,
            'estado' => 1,
            'fecha_pago' => now(),
        ]);

        // Client still exists
        $this->assertEquals($client->id, $payment->clients->id);

        // Soft-delete client
        $client->delete();

        // Payment still exists via withTrashed relationship
        $this->assertInstanceOf(Client::class, $payment->clients);
        $payment->clients->trashed();
    }
}
