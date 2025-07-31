<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
          // Créer 10 utilisateurs factices
        for ($i = 1; $i <= 10; $i++) {
            User::create([
                'name' => "User{$i}",
                'surname' => "Surname{$i}",
                'email' => "user{$i}@example.com",
                'password' => Hash::make('password'), // mot de passe commun
                'role' => 'student',
            ]);
        }
    }
}
