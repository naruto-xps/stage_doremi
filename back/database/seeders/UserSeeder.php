<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Récupérer les rôles existants
        $role_student = Role::where('name', 'student')->first();
        $role_teacher = Role::where('name', 'teacher')->first();
        $role_admin = Role::where('name', 'admin')->first();

        // Créer des utilisateurs avec des rôles spécifiques

        User::create([
            'name' => 'John',
            'surname' => 'Doe',  // S'assurer que surname est renseigné
            'email' => 'student@example.com',
            'password' => Hash::make('password'),
            'role_id' => $role_student->id,
            'is_premium' => false,
            'profile_photo' => 'path/to/photo.jpg',
            'bio' => 'Student bio...',
            'level' => 'Beginner',
            'school' => 'Sample School',
            'skills' => 'JavaScript, HTML, CSS',
            'experience' => '1 year of experience in web development',
            'linkedin' => 'https://www.linkedin.com/in/johndoe',
            'cv_path' => 'path/to/cv.pdf',
            'identity_number' => '123456789',
        ]);

        User::create([
            'name' => 'Jane',
            'surname' => 'Smith',  // S'assurer que surname est renseigné
            'email' => 'teacher@example.com',
            'password' => Hash::make('password'),
            'role_id' => $role_teacher->id,
            'is_premium' => true,
            'premium_expires_at' => now()->addYear(),
            'profile_photo' => 'path/to/photo.jpg',
            'bio' => 'Experienced teacher in web development',
            'level' => 'Advanced',
            'school' => 'Sample University',
            'skills' => 'React, Node.js, MongoDB',
            'experience' => '5 years of teaching web development',
            'linkedin' => 'https://www.linkedin.com/in/janesmith',
            'cv_path' => 'path/to/cv.pdf',
            'identity_number' => '987654321',
        ]);

        User::create([
            'name' => 'Admin',
            'surname' => 'User',  // S'assurer que surname est renseigné
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role_id' => $role_admin->id,
            'is_premium' => true,
            'premium_expires_at' => now()->addYear(),
            'profile_photo' => 'path/to/photo.jpg',
            'bio' => 'Administrator of Doremi Academy',
            'level' => 'Admin',
            'school' => 'Doremi Academy',
            'skills' => 'PHP, Laravel, MySQL',
            'experience' => '5 years of admin experience',
            'linkedin' => 'https://www.linkedin.com/in/adminuser',
            'cv_path' => 'path/to/cv.pdf',
            'identity_number' => '123098765',
        ]);

        // Créer 7 utilisateurs supplémentaires avec des rôles aléatoires
        User::factory(7)->create();
    }
}
