<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Progression extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'user_id', 'course_id', 'completed_chapters', 'completed_at'
    ];

    protected $casts = [
        'completed_chapters' => 'array',
        'completed_at' => 'datetime',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function course() {
        return $this->belongsTo(Course::class);
    }
}
