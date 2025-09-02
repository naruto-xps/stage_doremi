<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'description',
        'type', // 'pdf', 'doc', 'ppt', 'video', 'audio', 'book'
        'category',
        'genre',
        'upload_date',
        'size',
        'file_url',
        'file_name',
        'is_premium',
        'download_count',
        'tags',
        'image_url',
        'rating',
        'country',
        'instructor_id', // Pour les documents créés par les instructeurs
        'status',
        'views',
        'duration', // Pour les vidéos
        'thumbnail' // Pour les vidéos
    ];

    protected $casts = [
        'upload_date' => 'date',
        'is_premium' => 'boolean',
        'download_count' => 'integer',
        'views' => 'integer',
        'rating' => 'decimal:1',
        'tags' => 'array',
        'status' => 'string'
    ];

    // Relations
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function downloads()
    {
        return $this->hasMany(DocumentDownload::class);
    }

    public function views()
    {
        return $this->hasMany(DocumentView::class);
    }

    public function ratings()
    {
        return $this->hasMany(DocumentRating::class);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByGenre($query, $genre)
    {
        return $query->where('genre', $genre);
    }

    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }

    public function scopeByInstructor($query, $instructorId)
    {
        return $query->where('instructor_id', $instructorId);
    }

    public function scopeByCountry($query, $country)
    {
        return $query->where('country', $country);
    }

    // Méthodes utilitaires
    public function incrementDownloads()
    {
        $this->increment('download_count');
    }

    public function incrementViews()
    {
        $this->increment('views');
    }

    public function getFormattedSizeAttribute()
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->size;
        $unit = 0;
        
        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }
        
        return round($size, 2) . ' ' . $units[$unit];
    }

    public function getFormattedDurationAttribute()
    {
        if (!$this->duration) return null;
        
        $minutes = floor($this->duration / 60);
        $seconds = $this->duration % 60;
        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    public function getTypeLabelAttribute()
    {
        $labels = [
            'book' => 'Livre',
            'pdf' => 'PDF',
            'doc' => 'Document',
            'ppt' => 'Présentation',
            'video' => 'Vidéo',
            'audio' => 'Audio'
        ];
        
        return $labels[$this->type] ?? ucfirst($this->type);
    }

    public function getFileIconAttribute()
    {
        $icons = [
            'book' => '📚',
            'pdf' => '📄',
            'doc' => '📝',
            'ppt' => '📊',
            'video' => '🎬',
            'audio' => '🎵'
        ];
        
        return $icons[$this->type] ?? '📄';
    }
}
