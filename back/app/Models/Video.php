<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'file_url',
        'file_name',
        'file_size',
        'duration',
        'thumbnail',
        'instructor_id',
        'category',
        'difficulty_level',
        'is_premium',
        'price',
        'views',
        'likes',
        'dislikes',
        'status',
        'tags'
    ];

    protected $casts = [
        'file_size' => 'integer',
        'duration' => 'integer',
        'views' => 'integer',
        'likes' => 'integer',
        'dislikes' => 'integer',
        'is_premium' => 'boolean',
        'price' => 'decimal:2',
        'tags' => 'array',
        'status' => 'string'
    ];

    // Relations
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function views()
    {
        return $this->hasMany(VideoView::class);
    }

    public function likes()
    {
        return $this->hasMany(VideoLike::class);
    }

    public function comments()
    {
        return $this->hasMany(VideoComment::class);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }

    public function scopeByInstructor($query, $instructorId)
    {
        return $query->where('instructor_id', $instructorId);
    }

    // Méthodes utilitaires
    public function incrementViews()
    {
        $this->increment('views');
    }

    public function getFormattedDurationAttribute()
    {
        $minutes = floor($this->duration / 60);
        $seconds = $this->duration % 60;
        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    public function getFormattedFileSizeAttribute()
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $unit = 0;
        
        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }
        
        return round($size, 2) . ' ' . $units[$unit];
    }
}
