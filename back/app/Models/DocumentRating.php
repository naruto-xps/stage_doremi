<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'user_id',
        'rating', // 1 à 5 étoiles
        'comment',
        'created_at'
    ];

    protected $casts = [
        'rating' => 'integer',
        'created_at' => 'datetime'
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Validation du rating
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($rating) {
            if ($rating->rating < 1) $rating->rating = 1;
            if ($rating->rating > 5) $rating->rating = 5;
        });
    }
}
