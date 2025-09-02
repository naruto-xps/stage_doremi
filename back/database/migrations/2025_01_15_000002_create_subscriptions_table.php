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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('plan_type', ['basic', 'premium', 'enterprise']);
            $table->decimal('amount', 8, 2);
            $table->string('currency', 3)->default('EUR');
            $table->integer('duration_months');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['active', 'suspended', 'cancelled', 'expired'])->default('active');
            $table->boolean('auto_renew')->default(true);
            $table->string('payment_method');
            $table->text('description')->nullable();
            $table->json('features')->nullable(); // Fonctionnalités incluses dans le plan
            $table->timestamps();
            
            // Index pour les performances
            $table->index(['user_id', 'status']);
            $table->index(['status', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
