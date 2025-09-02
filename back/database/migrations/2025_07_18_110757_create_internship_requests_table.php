<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // demande de stage
        Schema::create('internship_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('internship_id')->constrained('internships')->onDelete('cascade');
            $table->text('motivation');
            
            // Informations utilisateur supplémentaires
            $table->string('user_name')->nullable();
            $table->string('user_email')->nullable();
            $table->enum('user_role', ['student', 'teacher'])->nullable();
            $table->string('user_phone')->nullable();
            
            // Fichiers et documents
            $table->text('cv_file_data')->nullable(); // Base64 du CV
            $table->string('cv_file_name')->nullable();
            $table->integer('cv_file_size')->nullable();
            $table->text('cover_letter')->nullable(); // Lettre de motivation (différent de motivation)
            
            // Gestion administrative
            $table->enum('status', ['pending', 'reviewed', 'accepted', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internship_requests');
    }
};
