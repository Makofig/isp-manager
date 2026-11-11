<?php

namespace Tests\Feature;

use App\Mail\ClientSuspendedMail;
use App\Mail\PaymentOverdueMail;
use App\Mail\QuotaGeneratedMail;
use App\Models\Client;
use App\Models\Payments;
use App\Models\Quota;
use App\Models\User;
use App\Notifications\ClientSuspended;
use App\Notifications\PaymentOverdue;
use App\Notifications\QuotaGenerated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    // =====================================================
    // QuotaGenerated Mail Tests
    // =====================================================

    /** @test */
    public function quota_generated_mail_renders_correctly(): void
    {
        $quota = Quota::create(['numero' => 8]);
        $mail = new QuotaGeneratedMail($quota, 25);

        $mail->assertSeeInHtml('Cuota Mensual Generada');
        $mail->assertSeeInHtml('25');
        $mail->assertSeeInHtml('#' . $quota->id);
    }

    /** @test */
    public function quota_generated_notification_sends_email(): void
    {
        Mail::fake();

        $quota = Quota::create(['numero' => 8]);
        $this->user->notify(new QuotaGenerated($quota, 25));

        Mail::assertSent(QuotaGeneratedMail::class, function ($mail) {
            return $mail->hasTo($this->user->email);
        });
    }

    // =====================================================
    // PaymentOverdue Mail Tests
    // =====================================================

    /** @test */
    public function payment_overdue_mail_renders_correctly(): void
    {
        $client = Client::factory()->create();
        $payment = Payments::factory()->create([
            'id_cliente' => $client->id,
            'costo' => 150.00,
        ]);
        $mail = new PaymentOverdueMail($payment, 45);

        $mail->assertSeeInHtml('Pago Vencido');
        $mail->assertSeeInHtml('45 días');
        $mail->assertSeeInHtml('150.00');
    }

    /** @test */
    public function payment_overdue_notification_via_includes_mail(): void
    {
        $notification = new PaymentOverdue(1, 1, 30, 100.00);

        $this->assertContains('database', $notification->via($this->user));
        $this->assertContains('mail', $notification->via($this->user));
    }

    /** @test */
    public function payment_overdue_notification_sends_email(): void
    {
        Mail::fake();

        $client = Client::factory()->create();
        $payment = Payments::factory()->create(['id_cliente' => $client->id]);
        $this->user->notify(new PaymentOverdue($client->id, $payment->id, 30, 100.00));

        Mail::assertSent(PaymentOverdueMail::class, function ($mail) {
            return $mail->hasTo($this->user->email);
        });
    }

    /** @test */
    public function payment_overdue_to_mail_returns_mailable(): void
    {
        $client = Client::factory()->create();
        $payment = Payments::factory()->create([
            'id_cliente' => $client->id,
            'costo' => 200.00,
        ]);
        $notification = new PaymentOverdue($client->id, $payment->id, 45, 200.00);

        $mailable = $notification->toMail($this->user);

        $this->assertInstanceOf(PaymentOverdueMail::class, $mailable);
    }

    // =====================================================
    // ClientSuspended Mail Tests
    // =====================================================

    /** @test */
    public function client_suspended_mail_renders_correctly(): void
    {
        $client = Client::factory()->create();
        $mail = new ClientSuspendedMail($client, 'Pago vencido por más de 90 días');

        $mail->assertSeeInHtml('Servicio Suspendido');
        $mail->assertSeeInHtml('Pago vencido por más de 90 días');
        $mail->assertSeeInHtml($client->nombre);
    }

    /** @test */
    public function client_suspended_notification_via_includes_mail(): void
    {
        $notification = new ClientSuspended(1, 'Falta de pago', now()->toDateTimeString());

        $this->assertContains('database', $notification->via($this->user));
        $this->assertContains('mail', $notification->via($this->user));
    }

    /** @test */
    public function client_suspended_notification_sends_email(): void
    {
        Mail::fake();

        $client = Client::factory()->create();
        $this->user->notify(new ClientSuspended($client->id, 'Falta de pago', now()->toDateTimeString()));

        Mail::assertSent(ClientSuspendedMail::class, function ($mail) {
            return $mail->hasTo($this->user->email);
        });
    }

    /** @test */
    public function client_suspended_to_mail_returns_mailable(): void
    {
        $client = Client::factory()->create();
        $notification = new ClientSuspended($client->id, 'Falta de pago', now()->toDateTimeString());

        $mailable = $notification->toMail($this->user);

        $this->assertInstanceOf(ClientSuspendedMail::class, $mailable);
    }

    // =====================================================
    // Command Tests
    // =====================================================

    /** @test */
    public function overdue_command_finds_and_notifies(): void
    {
        Mail::fake();

        $client = Client::factory()->create();
        // Create an old unpaid payment (45 days ago)
        $payment = Payments::factory()->create([
            'id_cliente' => $client->id,
            'estado' => 0,
            'fecha_pago' => null,
            'created_at' => now()->subDays(45),
        ]);

        $this->artisan('payments:remind-overdue --days=30')
            ->expectsOutputToContain('pagos vencidos notificados')
            ->assertExitCode(0);

        Mail::assertSent(PaymentOverdueMail::class);
    }

    /** @test */
    public function overdue_command_finds_nothing_when_no_overdue(): void
    {
        Payments::factory()->create([
            'estado' => 1, // paid
            'fecha_pago' => now(),
        ]);

        $this->artisan('payments:remind-overdue --days=30')
            ->expectsOutputToContain('No se encontraron pagos vencidos')
            ->assertExitCode(0);
    }

    /** @test */
    public function client_email_notification_contains_client_data(): void
    {
        Mail::fake();

        $quota = Quota::create(['numero' => 3]);
        $this->user->notify(new QuotaGenerated($quota, 50));

        Mail::assertSent(QuotaGeneratedMail::class, function ($mail) use ($quota) {
            return $mail->quota->id === $quota->id && $mail->clientCount === 50;
        });
    }

    /** @test */
    public function email_from_address_uses_env_config(): void
    {
        config(['mail.from.address' => 'noreply@intersys.com']);
        config(['mail.from.name' => 'InterSys']);

        $quota = Quota::create(['numero' => 8]);
        $mail = new QuotaGeneratedMail($quota, 25);

        $this->assertEquals('noreply@intersys.com', $mail->envelope()->from->address);
    }
}
