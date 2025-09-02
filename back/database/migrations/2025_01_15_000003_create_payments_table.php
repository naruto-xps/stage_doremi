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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 8, 2);
            $table->string('currency', 3);
            $table->string('payment_method');
            $table->string('transaction_id')->unique();
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->string('gateway')->nullable(); // Stripe, PayPal, etc.
            $table->string('gateway_transaction_id')->nullable(); // ID de transaction du gateway
            $table->json('metadata')->nullable(); // Données supplémentaires
            $table->text('failure_reason')->nullable(); // Raison de l'échec si applicable
            $table->timestamp('processed_at')->nullable(); // Quand le paiement a été traité
            $table->timestamps();
            
            // Index pour les performances
            $table->index(['user_id', 'status']);
            $table->index(['subscription_id']);
            $table->index(['transaction_id']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
