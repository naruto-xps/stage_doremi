<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_id',
        'amount',
        'currency',
        'payment_method',
        'transaction_id',
        'status',
        'gateway',
        'gateway_transaction_id',
        'metadata',
        'failure_reason',
        'processed_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'processed_at' => 'datetime'
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec l'abonnement
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Vérifier si le paiement est réussi
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Vérifier si le paiement a échoué
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Vérifier si le paiement est en attente
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Vérifier si le paiement a été remboursé
     */
    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }

    /**
     * Marquer le paiement comme traité
     */
    public function markAsProcessed(): bool
    {
        return $this->update(['processed_at' => now()]);
    }

    /**
     * Scope pour les paiements réussis
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope pour les paiements échoués
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope pour les paiements en attente
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Obtenir le montant formaté avec la devise
     */
    public function getFormattedAmount(): string
    {
        return $this->amount . ' ' . $this->currency;
    }
}
