<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    //
 use HasFactory;
    protected $fillable = [
        'course_id', 'title', 'content', 'video_url', 'pdf_path', 'audio_path', 'order'
    ];

    public function course() {
        return $this->belongsTo(Course::class);
    }
}
