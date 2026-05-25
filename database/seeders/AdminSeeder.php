<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Seed the admin account.
     * Uses updateOrCreate so it is safe to run multiple times.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@priyamfinserv.com')],
            [
                'name'              => env('ADMIN_NAME', 'Admin'),
                'password'          => Hash::make(env('ADMIN_PASSWORD', 'changeme123')),
                'role'              => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Admin account seeded successfully.');
    }
}
