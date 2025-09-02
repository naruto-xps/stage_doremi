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
        // offre de stage
        Schema::create('internships', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('location');
            $table->string('company');
            $table->string('duration')->nullable();
            $table->json('requirements')->nullable();
            $table->date('application_deadline')->nullable();
            $table->string('salary')->nullable();
            $table->enum('type', ['stage', 'alternance', 'emploi'])->default('stage');
            $table->boolean('remote')->default(false);
            $table->boolean('is_premium')->default(false);
            $table->integer('views')->default(0);
            $table->integer('applications')->default(0);
            $table->decimal('rating', 3, 2)->nullable();
            $table->string('logo')->nullable();
            $table->string('image')->nullable();
            $table->json('tags')->nullable();
            $table->foreignId('recruiter_id')->constrained('users');
            $table->enum('status', ['active', 'expired', 'closed'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internships');
    }
};
