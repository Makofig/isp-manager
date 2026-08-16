<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Client permissions
            'clients.view',
            'clients.create',
            'clients.edit',
            'clients.delete',
            'clients.ban',
            'clients.export',

            // Contract permissions
            'contracts.view',
            'contracts.create',
            'contracts.edit',
            'contracts.delete',

            // Access Point permissions
            'access-points.view',
            'access-points.create',
            'access-points.edit',
            'access-points.delete',

            // Payment permissions
            'payments.view',
            'payments.create',
            'payments.edit',
            'payments.delete',

            // Quota permissions
            'quotas.view',
            'quotas.create',

            // Statistics
            'statistics.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Admin role with all permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());

        // Create Operator role with limited permissions
        $operatorRole = Role::firstOrCreate(['name' => 'operator']);
        $operatorRole->syncPermissions([
            'clients.view', 'clients.create', 'clients.edit',
            'payments.view', 'payments.create', 'payments.edit',
            'quotas.view', 'quotas.create',
            'statistics.view',
        ]);

        // Create Technician role (infrastructure only)
        $techRole = Role::firstOrCreate(['name' => 'technician']);
        $techRole->syncPermissions([
            'access-points.view', 'access-points.create', 'access-points.edit',
            'clients.view',
        ]);

        // Assign admin role to first user if exists
        $user = User::first();
        if ($user && !$user->hasRole('admin')) {
            $user->assignRole('admin');
        }
    }
}
