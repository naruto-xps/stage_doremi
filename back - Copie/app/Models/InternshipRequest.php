<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternshipRequest extends Model
{
    //
     use HasFactory;

    protected $fillable = [
        'user_id', 'internship_id', 'motivation', 'status'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function internship() {
        return $this->belongsTo(Internship::class);
    }
}
