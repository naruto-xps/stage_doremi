<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Internship extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'location',
        'company',
        'duration',
        'requirements',
        'application_deadline',
        'salary',
        'type',
        'remote',
        'is_premium',
        'views',
        'applications',
        'rating',
        'logo',
        'image',
        'tags',
        'recruiter_id',
        'status'
    ];

    protected $casts = [
        'requirements' => 'array',
        'tags' => 'array',
        'remote' => 'boolean',
        'is_premium' => 'boolean',
        'views' => 'integer',
        'applications' => 'integer',
        'rating' => 'decimal:2',
        'application_deadline' => 'date',
        'status' => 'string'
    ];

    public function requests()
    {
        return $this->hasMany(InternshipRequest::class);
    }

    public function recruiter()
    {
        return $this->belongsTo(User::class, 'recruiter_id');
    }
}
