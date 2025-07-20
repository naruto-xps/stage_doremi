<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Internship extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'location',
        'company'
    ];

    public function requests()
    {
        return $this->hasMany(InternshipRequest::class);
    }
}
