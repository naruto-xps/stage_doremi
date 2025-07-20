<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        //     'password' => bcrypt('password'), // Password is 'password'
        //     'role_id' => 1, // Assuming role_id 1 is for Admin
        // ]);
         $this->call([
        RoleSeeder::class,       // Crée des rôles
         UserSeeder::class,       // Crée des utilisateurs

    ]);
    }
}
