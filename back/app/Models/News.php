<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'content', 'type', 'is_published',
        'image', 'author', 'location', 'read_time',
        'priority', 'status', 'is_featured', 'is_urgent',
        'views_count', 'likes_count', 'comments_count', 'shares_count',
        'published_at', 'expires_at', 'publish_schedule', 'author_id',
        'category', 'subcategory', 'tags', 'target_audience',
        'excerpt', 'meta_description', 'meta_keywords', 'featured_image', 'gallery'
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'is_urgent' => 'boolean',
        'tags' => 'array',
        'target_audience' => 'array',
        'gallery' => 'array',
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
        'publish_schedule' => 'datetime'
    ];

    protected $dates = [
        'published_at',
        'expires_at',
        'publish_schedule',
        'created_at',
        'updated_at'
    ];

    // Relations
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // Scopes
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)
                    ->where('status', 'published')
                    ->where(function($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeByPriority(Builder $query, string $priority): Builder
    {
        return $query->where('priority', $priority);
    }

    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeUrgent(Builder $query): Builder
    {
        return $query->where('is_urgent', true);
    }

    public function scopeNotExpired(Builder $query): Builder
    {
        return $query->where(function($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    public function scopeScheduled(Builder $query): Builder
    {
        return $query->whereNotNull('publish_schedule')
                    ->where('publish_schedule', '<=', now());
    }

    // Accesseurs
    public function getFormattedReadTimeAttribute(): string
    {
        return $this->read_time ?: '2 min';
    }

    public function getFormattedViewsAttribute(): string
    {
        return number_format($this->views_count);
    }

    public function getFormattedLikesAttribute(): string
    {
        return number_format($this->likes_count);
    }

    public function getFormattedCommentsAttribute(): string
    {
        return number_format($this->comments_count);
    }

    public function getFormattedSharesAttribute(): string
    {
        return number_format($this->shares_count);
    }

    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'high' => 'red',
            'medium' => 'yellow',
            'low' => 'green',
            default => 'gray'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'published' => 'green',
            'draft' => 'yellow',
            'archived' => 'gray',
            default => 'blue'
        };
    }

    // Mutateurs
    public function setTagsAttribute($value): void
    {
        $this->attributes['tags'] = is_array($value) ? json_encode($value) : $value;
    }

    public function setTargetAudienceAttribute($value): void
    {
        $this->attributes['target_audience'] = is_array($value) ? json_encode($value) : $value;
    }

    public function setGalleryAttribute($value): void
    {
        $this->attributes['gallery'] = is_array($value) ? json_encode($value) : $value;
    }

    // Méthodes
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function incrementLikes(): void
    {
        $this->increment('likes_count');
    }

    public function incrementComments(): void
    {
        $this->increment('comments_count');
    }

    public function incrementShares(): void
    {
        $this->increment('shares_count');
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isScheduled(): bool
    {
        return $this->publish_schedule && $this->publish_schedule->isFuture();
    }

    public function canBePublished(): bool
    {
        return $this->status === 'draft' && !$this->isScheduled();
    }

    public function publish(): void
    {
        $this->update([
            'status' => 'published',
            'published_at' => now(),
            'publish_schedule' => null
        ]);
    }

    public function archive(): void
    {
        $this->update(['status' => 'archived']);
    }

    public function unarchive(): void
    {
        $this->update(['status' => 'published']);
    }

    public function toggleFeatured(): void
    {
        $this->update(['is_featured' => !$this->is_featured]);
    }

    public function toggleUrgent(): void
    {
        $this->update(['is_urgent' => !$this->is_urgent]);
    }
}
