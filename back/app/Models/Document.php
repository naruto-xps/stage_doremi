<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'title', 'type', 'theme', 'school', 'file_path', 'is_premium'
    ];
}
