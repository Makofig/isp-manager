<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Contracts;
use App\Models\Client;
use App\Models\Access_Point;
use App\Models\Payments;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PolicyTest extends TestCase
{
    use RefreshDatabase;

    // =====================================================
    // Admin Role Tests
    // =====================================================

    /** @test */
    public function admin_can_do_everything(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create();
        $contract = Contracts::factory()->create();
        $accessPoint = Access_Point::factory()->create();
        $payment = Payments::factory()->create();

        // Client
        $this->assertTrue($admin->can('viewAny', Client::class));
        $this->assertTrue($admin->can('view', $client));
        $this->assertTrue($admin->can('create', Client::class));
        $this->assertTrue($admin->can('update', $client));
        $this->assertTrue($admin->can('delete', $client));
        $this->assertTrue($admin->can('ban', $client));

        // Contract
        $this->assertTrue($admin->can('viewAny', Contracts::class));
        $this->assertTrue($admin->can('view', $contract));
        $this->assertTrue($admin->can('create', Contracts::class));
        $this->assertTrue($admin->can('update', $contract));
        $this->assertTrue($admin->can('delete', $contract));

        // Access Point
        $this->assertTrue($admin->can('viewAny', Access_Point::class));
        $this->assertTrue($admin->can('view', $accessPoint));
        $this->assertTrue($admin->can('create', Access_Point::class));
        $this->assertTrue($admin->can('update', $accessPoint));
        $this->assertTrue($admin->can('delete', $accessPoint));

        // Payment
        $this->assertTrue($admin->can('viewAny', Payments::class));
        $this->assertTrue($admin->can('view', $payment));
        $this->assertTrue($admin->can('update', $payment));
        $this->assertTrue($admin->can('delete', $payment));
    }

    // =====================================================
    // Operator Role Tests
    // =====================================================

    /** @test */
    public function operator_can_view_and_create_but_not_delete(): void
    {
        $operator = User::factory()->create(['role' => 'operator']);
        $client = Client::factory()->create();
        $contract = Contracts::factory()->create();
        $accessPoint = Access_Point::factory()->create();
        $payment = Payments::factory()->create();

        // Can view
        $this->assertTrue($operator->can('viewAny', Client::class));
        $this->assertTrue($operator->can('view', $client));
        $this->assertTrue($operator->can('viewAny', Contracts::class));
        $this->assertTrue($operator->can('view', $contract));

        // Can create
        $this->assertTrue($operator->can('create', Client::class));
        $this->assertTrue($operator->can('create', Contracts::class));
        $this->assertTrue($operator->can('create', Access_Point::class));

        // Can update
        $this->assertTrue($operator->can('update', $client));
        $this->assertTrue($operator->can('update', $contract));
        $this->assertTrue($operator->can('update', $accessPoint));
        $this->assertTrue($operator->can('update', $payment));

        // CANNOT delete
        $this->assertFalse($operator->can('delete', $client));
        $this->assertFalse($operator->can('delete', $contract));
        $this->assertFalse($operator->can('delete', $accessPoint));
        $this->assertFalse($operator->can('delete', $payment));
    }

    /** @test */
    public function operator_can_ban_clients(): void
    {
        $operator = User::factory()->create(['role' => 'operator']);
        $client = Client::factory()->create();

        $this->assertTrue($operator->can('ban', $client));
    }

    // =====================================================
    // Auditor Role Tests
    // =====================================================

    /** @test */
    public function auditor_can_only_view(): void
    {
        $auditor = User::factory()->create(['role' => 'auditor']);
        $client = Client::factory()->create();
        $contract = Contracts::factory()->create();
        $accessPoint = Access_Point::factory()->create();
        $payment = Payments::factory()->create();

        // Can view
        $this->assertTrue($auditor->can('viewAny', Client::class));
        $this->assertTrue($auditor->can('view', $client));
        $this->assertTrue($auditor->can('viewAny', Contracts::class));
        $this->assertTrue($auditor->can('view', $contract));
        $this->assertTrue($auditor->can('viewAny', Access_Point::class));
        $this->assertTrue($auditor->can('view', $accessPoint));
        $this->assertTrue($auditor->can('viewAny', Payments::class));
        $this->assertTrue($auditor->can('view', $payment));

        // CANNOT create
        $this->assertFalse($auditor->can('create', Client::class));
        $this->assertFalse($auditor->can('create', Contracts::class));
        $this->assertFalse($auditor->can('create', Access_Point::class));

        // CANNOT update
        $this->assertFalse($auditor->can('update', $client));
        $this->assertFalse($auditor->can('update', $contract));
        $this->assertFalse($auditor->can('update', $accessPoint));
        $this->assertFalse($auditor->can('update', $payment));

        // CANNOT delete
        $this->assertFalse($auditor->can('delete', $client));
        $this->assertFalse($auditor->can('delete', $contract));
        $this->assertFalse($auditor->can('delete', $accessPoint));
        $this->assertFalse($auditor->can('delete', $payment));

        // CANNOT ban
        $this->assertFalse($auditor->can('ban', $client));
    }

    // =====================================================
    // HTTP Authorization Tests
    // =====================================================

    /** @test */
    public function operator_cannot_delete_client_via_http(): void
    {
        $operator = User::factory()->create(['role' => 'operator']);
        $client = Client::factory()->create();

        $response = $this->actingAs($operator)
            ->delete(route('clients.destroy', $client->id));

        $response->assertStatus(403);
        $this->assertDatabaseHas('cliente', ['id' => $client->id]);
    }

    /** @test */
    public function admin_can_delete_client_via_http(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create();

        $response = $this->actingAs($admin)
            ->delete(route('clients.destroy', $client->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('cliente', ['id' => $client->id]);
    }

    /** @test */
    public function auditor_cannot_create_client_via_http(): void
    {
        $auditor = User::factory()->create(['role' => 'auditor']);
        $contract = Contracts::factory()->create();
        $accessPoint = Access_Point::factory()->create();

        $response = $this->actingAs($auditor)
            ->post(route('clients.store'), [
                'first_name' => 'Juan',
                'last_name' => 'Pérez',
                'ip_address' => '192.168.1.100',
                'phone' => '1234567890',
                'contracts_id' => $contract->id,
                'access_point_id' => $accessPoint->id,
                'street_address' => 'Calle 123',
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function operator_cannot_delete_contract_via_http(): void
    {
        $operator = User::factory()->create(['role' => 'operator']);
        $contract = Contracts::factory()->create();

        $response = $this->actingAs($operator)
            ->delete(route('contracts.destroy', $contract->id));

        $response->assertStatus(403);
        $this->assertDatabaseHas('plan', ['id' => $contract->id]);
    }

    /** @test */
    public function operator_cannot_delete_access_point_via_http(): void
    {
        $operator = User::factory()->create(['role' => 'operator']);
        $accessPoint = Access_Point::factory()->create();

        $response = $this->actingAs($operator)
            ->delete(route('access-point.destroy', $accessPoint->id));

        $response->assertStatus(403);
        $this->assertDatabaseHas('accespoint', ['id' => $accessPoint->id]);
    }

    /** @test */
    public function admin_can_delete_access_point_via_http(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $accessPoint = Access_Point::factory()->create();

        $response = $this->actingAs($admin)
            ->delete(route('access-point.destroy', $accessPoint->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('accespoint', ['id' => $accessPoint->id]);
    }

    // =====================================================
    // User Role Helper Tests
    // =====================================================

    /** @test */
    public function user_role_helpers_work_correctly(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $operator = User::factory()->create(['role' => 'operator']);
        $auditor = User::factory()->create(['role' => 'auditor']);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isOperator());
        $this->assertFalse($admin->isAuditor());

        $this->assertTrue($operator->isOperator());
        $this->assertFalse($operator->isAdmin());

        $this->assertTrue($auditor->isAuditor());
        $this->assertFalse($auditor->isAdmin());

        $this->assertTrue($admin->hasRole('admin'));
        $this->assertFalse($admin->hasRole('operator'));
    }

    /** @test */
    public function user_default_role_is_admin(): void
    {
        $user = User::factory()->create();

        $this->assertEquals('admin', $user->role);
        $this->assertTrue($user->isAdmin());
    }

    /** @test */
    public function gate_before_allows_admin_everything(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Admin bypasses all gate checks via Gate::before
        $this->assertTrue($admin->can('view-stats'));
        $this->assertTrue($admin->can('export-pdf'));
    }

    /** @test */
    public function auditor_can_view_stats(): void
    {
        $auditor = User::factory()->create(['role' => 'auditor']);

        $this->assertTrue($auditor->can('view-stats'));
        $this->assertTrue($auditor->can('export-pdf'));
    }
}
