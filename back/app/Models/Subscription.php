<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_type',
        'amount',
        'currency',
        'duration_months',
        'start_date',
        'end_date',
        'status',
        'auto_renew',
        'payment_method',
        'description',
        'features'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'auto_renew' => 'boolean',
        'features' => 'array',
        'amount' => 'decimal:2'
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec les paiements
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Vérifier si l'abonnement est actif
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->end_date->isFuture();
    }

    /**
     * Vérifier si l'abonnement est expiré
     */
    public function isExpired(): bool
    {
        return $this->end_date->isPast();
    }

    /**
     * Obtenir le temps restant en jours
     */
    public function getDaysRemaining(): int
    {
        if ($this->isExpired()) {
            return 0;
        }
        
        return now()->diffInDays($this->end_date, false);
    }

    /**
     * Scope pour les abonnements actifs
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('end_date', '>', now());
    }

    /**
     * Scope pour les abonnements expirés
     */
    public function scopeExpired($query)
    {
        return $query->where('end_date', '<=', now());
    }
}
