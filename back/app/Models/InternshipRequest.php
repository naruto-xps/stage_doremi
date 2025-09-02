<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternshipRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'internship_id',
        'motivation',
        'user_name',
        'user_email',
        'user_role',
        'user_phone',
        'cv_file_data',
        'cv_file_name',
        'cv_file_size',
        'cover_letter',
        'status',
        'admin_notes'
    ];

    protected $casts = [
        'cv_file_size' => 'integer',
        'status' => 'string'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function internship() {
        return $this->belongsTo(Internship::class);
    }
}
