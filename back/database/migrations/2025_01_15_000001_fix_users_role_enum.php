<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Vérifier si la colonne role existe
        if (Schema::hasColumn('users', 'role')) {
            // Pour PostgreSQL, on utilise une approche simple
            if (DB::connection()->getDriverName() === 'pgsql') {
                // Convertir temporairement en varchar pour éviter les problèmes d'enum
                DB::statement("ALTER TABLE users ALTER COLUMN role TYPE VARCHAR(50)");
                
                // Mettre à jour les valeurs existantes si nécessaire
                DB::statement("UPDATE users SET role = 'user' WHERE role NOT IN ('admin', 'teacher', 'student', 'recruiter', 'user')");
            } else {
                // Pour MySQL, on peut modifier directement
                DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'teacher', 'student', 'recruiter', 'user') DEFAULT 'user'");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Pour PostgreSQL, on ne peut pas facilement revenir en arrière
        // Pour MySQL, on peut revenir à l'état précédent
        if (DB::connection()->getDriverName() !== 'pgsql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'teacher', 'student', 'user') DEFAULT 'user'");
        }
    }
}; 