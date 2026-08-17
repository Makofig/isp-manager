<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Quota;
use App\Notifications\QuotaGenerated;
use App\Mail\QuotaGeneratedMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /** @test */
    public function migration_creates_notifications_table_with_correct_schema(): void
    {
        $this->assertTrue(
            \Illuminate\Support\Facades\Schema::hasTable('notifications')
        );

        $columns = \Illuminate\Support\Facades\Schema::getColumns('notifications');
        $columnNames = array_column($columns, 'name');

        $this->assertContains('id', $columnNames);
        $this->assertContains('type', $columnNames);
        $this->assertContains('notifiable_type', $columnNames);
        $this->assertContains('notifiable_id', $columnNames);
        $this->assertContains('data', $columnNames);
        $this->assertContains('read_at', $columnNames);
        $this->assertContains('created_at', $columnNames);
        $this->assertContains('updated_at', $columnNames);
    }

    /** @test */
    public function quota_generated_notification_can_be_created(): void
    {
        $quota = Quota::create(['numero' => 8]);
        $notification = new QuotaGenerated($quota, 15);

        // via() includes both database and mail channels
        $this->assertContains('database', $notification->via($this->user));
        $this->assertContains('mail', $notification->via($this->user));

        $array = $notification->toArray($this->user);
        $this->assertEquals($quota->id, $array['quota_id']);
        $this->assertEquals(8, $array['month_year']);
        $this->assertEquals(15, $array['client_count']);
    }

    /** @test */
    public function notification_can_be_stored_in_database(): void
    {
        $quota = Quota::create(['numero' => 8]);
        $this->user->notify(new QuotaGenerated($quota, 10));

        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $this->user->id,
        ]);

        $notification = $this->user->notifications()->first();
        $this->assertNotNull($notification);
        $this->assertNull($notification->read_at);
        $this->assertEquals(10, $notification->data['client_count']);
    }

    /** @test */
    public function notification_mark_as_read_sets_read_at(): void
    {
        $quota = Quota::create(['numero' => 8]);
        $this->user->notify(new QuotaGenerated($quota, 10));

        $notification = $this->user->notifications()->first();
        $notification->markAsRead();

        $this->assertNotNull($notification->fresh()->read_at);
    }

    /** @test */
    public function unread_notifications_count_works(): void
    {
        $quota = Quota::create(['numero' => 8]);

        $this->user->notify(new QuotaGenerated($quota, 10));
        $this->user->notify(new QuotaGenerated($quota, 20));
        $this->user->notify(new QuotaGenerated($quota, 30));

        $this->assertEquals(3, $this->user->unreadNotifications()->count());
    }

    /** @test */
    public function notifications_index_returns_200_for_authenticated_user(): void
    {
        $quota = Quota::create(['numero' => 8]);
        $this->user->notify(new QuotaGenerated($quota, 10));

        $response = $this->actingAs($this->user)
            ->get(route('notifications.index'));

        $response->assertStatus(200);
        $response->assertViewIs('notifications.index');
    }

    /** @test */
    public function notifications_index_requires_authentication(): void
    {
        $response = $this->get(route('notifications.index'));

        $response->assertRedirect('/login');
    }

    /** @test */
    public function mark_all_read_clears_unread_notifications(): void
    {
        $quota = Quota::create(['numero' => 8]);
        $this->user->notify(new QuotaGenerated($quota, 10));
        $this->user->notify(new QuotaGenerated($quota, 20));

        $response = $this->actingAs($this->user)
            ->post(route('notifications.read-all'));

        $response->assertRedirect();
        $this->assertEquals(0, $this->user->unreadNotifications()->count());
    }

    /** @test */
    public function mark_single_read_sets_read_at(): void
    {
        $quota = Quota::create(['numero' => 8]);
        $this->user->notify(new QuotaGenerated($quota, 10));

        $notification = $this->user->notifications()->first();

        $response = $this->actingAs($this->user)
            ->post(route('notifications.read', $notification));

        $response->assertRedirect();
        $this->assertNotNull($notification->fresh()->read_at);
    }

    /** @test */
    public function cannot_mark_other_users_notification(): void
    {
        $quota = Quota::create(['numero' => 8]);
        $otherUser = User::factory()->create();
        $otherUser->notify(new QuotaGenerated($quota, 10));

        $notification = $otherUser->notifications()->first();

        $response = $this->actingAs($this->user)
            ->post(route('notifications.read', $notification));

        $response->assertStatus(403);
        $this->assertNull($notification->fresh()->read_at);
    }

    /** @test */
    public function notification_via_includes_database_and_mail(): void
    {
        $quota = Quota::create(['numero' => 8]);
        $notification = new QuotaGenerated($quota, 15);

        $this->assertEquals(['database', 'mail'], $notification->via($this->user));
    }

    /** @test */
    public function quota_generated_toMail_returns_mailable(): void
    {
        $quota = Quota::create(['numero' => 8]);
        $notification = new QuotaGenerated($quota, 15);

        $mailable = $notification->toMail($this->user);

        $this->assertInstanceOf(QuotaGeneratedMail::class, $mailable);
        $this->assertEquals($quota->id, $mailable->quota->id);
        $this->assertEquals(15, $mailable->clientCount);
    }

    /** @test */
    public function quota_generated_toMail_sends_to_user_email(): void
    {
        $quota = Quota::create(['numero' => 8]);
        $notification = new QuotaGenerated($quota, 15);

        $mailable = $notification->toMail($this->user);

        $this->assertTrue($mailable->hasTo($this->user->email));
    }

    /** @test */
    public function quota_generated_email_has_correct_subject(): void
    {
        $quota = Quota::create(['numero' => 8]);
        $notification = new QuotaGenerated($quota, 20);

        $mailable = $notification->toMail($this->user);
        $envelope = $mailable->envelope();

        $this->assertStringContainsString('Cuota generada', $envelope->subject);
    }

    // =====================================================
    // QuotaGenerationService Tests
    // =====================================================

    /** @test */
    public function service_creates_quota_for_new_month(): void
    {
        $service = app(\App\Services\QuotaGenerationService::class);

        $result = $service->handle(2025, 9);

        $this->assertTrue($result['created']);
        $this->assertNotNull($result['quota']);
        $this->assertEquals(9, $result['quota']->numero);
        $this->assertDatabaseHas('cuotas', ['numero' => 9]);
    }

    /** @test */
    public function service_prevents_duplicate_quota_for_same_month(): void
    {
        $service = app(\App\Services\QuotaGenerationService::class);

        // First call creates
        $result1 = $service->handle(2025, 10);
        $this->assertTrue($result1['created']);

        // Second call detects existing
        $result2 = $service->handle(2025, 10);
        $this->assertFalse($result2['created']);
        $this->assertNotNull($result2['quota']); // returns existing
        $this->assertEquals($result1['quota']->id, $result2['quota']->id);
    }

    /** @test */
    public function service_validates_month_range(): void
    {
        $service = app(\App\Services\QuotaGenerationService::class);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Mes inválido');

        $service->handle(2025, 13);
    }

    /** @test */
    public function service_allows_different_months(): void
    {
        $service = app(\App\Services\QuotaGenerationService::class);

        $result1 = $service->handle(2025, 1);
        $result2 = $service->handle(2025, 2);

        $this->assertTrue($result1['created']);
        $this->assertTrue($result2['created']);
        $this->assertNotEquals($result1['quota']->id, $result2['quota']->id);
    }

    /** @test */
    public function database_unique_constraint_rejects_duplicate_numero(): void
    {
        \App\Models\Quota::create(['numero' => 5]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        // Bypass service to test DB-level constraint directly
        \App\Models\Quota::create(['numero' => 5]);
    }

    /** @test */
    public function controller_creates_quota_via_service(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('quota.store'), [
                'months_year' => 'September 2025',
            ]);

        $response->assertRedirect(route('quota'));
        $this->assertDatabaseHas('cuotas', ['numero' => 9]);
    }

    /** @test */
    public function controller_rejects_duplicate_quota_via_service(): void
    {
        // Create first
        $this->actingAs($this->user)
            ->post(route('quota.store'), [
                'months_year' => 'October 2025',
            ]);

        // Try duplicate
        $response = $this->actingAs($this->user)
            ->post(route('quota.store'), [
                'months_year' => 'October 2025',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('quota');

        // Only one quota for October exists
        $this->assertEquals(1, \App\Models\Quota::where('numero', 10)->count());
    }
}
