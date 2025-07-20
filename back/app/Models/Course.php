<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    //
    use HasFactory;
    protected $fillable = [
        'title', 'description', 'theme', 'level', 'is_premium', 'teacher_id', 'school'
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
