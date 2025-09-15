<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    protected $fillable = [
        'news_id',
        'author_name',
        'author_email',
        'content',
        'is_approved',
        'likes_count'
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'likes_count' => 'integer'
    ];

    /**
     * Relation avec l'actualité
     */
    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class);
    }

    /**
     * Scope pour les commentaires approuvés
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Incrémenter les likes
     */
    public function incrementLikes(): void
    {
        $this->increment('likes_count');
    }
}
