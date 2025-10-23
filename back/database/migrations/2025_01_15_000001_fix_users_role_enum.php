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
            $driver = DB::connection()->getDriverName();
            
            if ($driver === 'pgsql') {
                // Pour PostgreSQL, on utilise une approche simple
                DB::statement("ALTER TABLE users ALTER COLUMN role TYPE VARCHAR(50)");
                DB::statement("UPDATE users SET role = 'user' WHERE role NOT IN ('admin', 'teacher', 'student', 'recruiter', 'user')");
            } elseif ($driver === 'mysql') {
                // Pour MySQL, on peut modifier directement
                DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'teacher', 'student', 'recruiter', 'user') DEFAULT 'user'");
            } elseif ($driver === 'sqlite') {
                // Pour SQLite, on ne peut pas modifier les colonnes directement
                // On met à jour les valeurs existantes si nécessaire
                DB::statement("UPDATE users SET role = 'user' WHERE role NOT IN ('admin', 'teacher', 'student', 'recruiter', 'user')");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'mysql') {
            // Pour MySQL, on peut revenir à l'état précédent
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'teacher', 'student', 'user') DEFAULT 'user'");
        }
        // Pour PostgreSQL et SQLite, on ne peut pas facilement revenir en arrière
    }
}; 