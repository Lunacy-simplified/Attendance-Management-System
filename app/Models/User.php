<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'passport_number',
        'name',
        'username',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'role' => UserRole::class,
    ];

    // Attendance records supervisor has entered
    public function recordedAttendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_user')
                    ->withPivot('assigned_at')
                    ->withTimestamps();
    }


    // helper to chek if admin
    public function isAdmin(): bool
    {
        return $this->role === UserRole::SUPERUSER;
    }
}
