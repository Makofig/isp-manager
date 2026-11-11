<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@admin.com');

        if (! User::where('email', $email)->exists()) {
            User::create([
                'name' => env('ADMIN_NAME', 'admin'),
                'email' => $email,
                'password' => Hash::make(env('ADMIN_PASSWORD', 'admin123456')),
            ]);
        }
    }
}
