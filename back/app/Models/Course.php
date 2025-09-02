<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    //
    use HasFactory;
    protected $fillable = [
        'title', 
        'description', 
        'thumbnail',
        'duration',
        'chapters_count',
        'rating',
        'students_count',
        'price',
        'theme', 
        'level', 
        'difficulty_level',
        'category',
        'education_level',
        'is_premium', 
        'teacher_id', 
        'school'
    ];

    protected $casts = [
        'is_premium' => 'boolean',
        'rating' => 'decimal:2',
        'price' => 'decimal:2',
        'chapters_count' => 'integer',
        'students_count' => 'integer',
    ];

    public function teacher() {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function chapters() {
        return $this->hasMany(Chapter::class);
    }

    public function progressions() {
        return $this->hasMany(Progression::class);
    }
}
