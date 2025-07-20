<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

      protected $fillable = [
        'name', 'surname', 'email', 'password', 'role_id', 'is_premium', 'premium_expires_at',
        'profile_photo', 'bio', 'level', 'school', 'skills', 'experience', 'linkedin',
        'cv_path', 'identity_number'
    ];

    protected $casts = [
        'is_premium' => 'boolean',
        'premium_expires_at' => 'datetime',
    ];

    public function role() {
        return $this->belongsTo(Role::class);
    }

    public function courses() {
        return $this->hasMany(Course::class, 'teacher_id');
    }

    public function progressions() {
        return $this->hasMany(Progression::class);
    }

    public function internshipRequests() {
        return $this->hasMany(InternshipRequest::class);
    }

    public function sentMessages() {
        return $this->hasMany(Message::class, 'sender_id');
    }
   
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
