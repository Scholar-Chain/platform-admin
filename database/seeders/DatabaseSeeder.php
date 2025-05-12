<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ShieldSeeder::class
        ]);

        User::factory()->create([
            'name' => 'Administrator',
            'email' => 'su@gmail.com',
            'password' => bcrypt('password'),
            'is_active' => true
        ])->assignRole(UserRole::SUPER_ADMIN->value);
    }
}
