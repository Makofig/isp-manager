<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Payments;
use App\Models\Quota;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function authenticated_user_can_view_payment_details(): void
    {
        $payment = Payments::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('payments.show', $payment->id));

        $response->assertStatus(200);
    }

    /** @test */
    public function authenticated_user_can_edit_payment(): void
    {
        $payment = Payments::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('payments.edit', $payment->id));

        $response->assertStatus(200);
    }

    /** @test */
    public function authenticated_user_can_update_payment(): void
    {
        $payment = Payments::factory()->create([
            'abonado' => 0,
            'estado' => 0,
        ]);

        $response = $this->actingAs($this->user)
            ->put(route('payments.update', $payment->id), [
                'amount' => 150.00,
                'coment' => 'Pago parcial',
                'payment_date' => now()->format('Y-m-d'),
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pagos', [
            'id' => $payment->id,
            'abonado' => 150.00,
            'estado' => 1,
        ]);
    }

    /** @test */
    public function payment_update_requires_non_negative_amount(): void
    {
        $payment = Payments::factory()->create();

        $response = $this->actingAs($this->user)
            ->put(route('payments.update', $payment->id), [
                'amount' => -50,
                'coment' => 'Test',
                'payment_date' => now()->format('Y-m-d'),
            ]);

        $response->assertSessionHasErrors('amount');
    }

    /** @test */
    public function payment_update_requires_date(): void
    {
        $payment = Payments::factory()->create();

        $response = $this->actingAs($this->user)
            ->put(route('payments.update', $payment->id), [
                'amount' => 100,
                'coment' => 'Test',
            ]);

        $response->assertSessionHasErrors('payment_date');
    }

    /** @test */
    public function payment_belongs_to_client(): void
    {
        $client = Client::factory()->create();
        $payment = Payments::factory()->create(['id_cliente' => $client->id]);

        $this->assertInstanceOf(Client::class, $payment->clients);
        $this->assertEquals($client->id, $payment->clients->id);
    }

    /** @test */
    public function payment_belongs_to_quota(): void
    {
        $quota = Quota::factory()->create();
        $payment = Payments::factory()->create(['id_cuota' => $quota->id]);

        $this->assertInstanceOf(Quota::class, $payment->cuota);
        $this->assertEquals($quota->id, $payment->cuota->id);
    }

    /** @test */
    public function payment_can_be_marked_as_paid(): void
    {
        $payment = Payments::factory()->create([
            'estado' => 0,
            'abonado' => 0,
        ]);

        $payment->update([
            'estado' => 1,
            'abonado' => $payment->costo,
            'fecha_pago' => now(),
        ]);

        $fresh = $payment->fresh();
        $this->assertTrue((bool) $fresh->estado);
        $this->assertNotNull($fresh->fecha_pago);
    }

    /** @test */
    public function payment_stores_voucher_image_path(): void
    {
        $payment = Payments::factory()->create();

        $payment->update(['image' => 'vouchers/voucher-123.jpg']);

        $this->assertEquals('vouchers/voucher-123.jpg', $payment->fresh()->image);
    }
}
