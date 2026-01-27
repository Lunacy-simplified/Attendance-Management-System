<?php

namespace App\Models;

use App\Enums\ProjectStatus;
use App\Enums\WorkerAssignmentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'status',
    ];

    protected $casts = [
        'status' => ProjectStatus::class,
    ];

    // supervisors assigned to this project
    public function supervisors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_user')
                    ->withPivot('assigned_at')
                    ->withTimestamps();
    }

    // Workers assigned to this project
    public function workers(): BelongsToMany
    {
        return $this->belongsToMany(Worker::class, 'project_workers')
                    ->withPivot(['assigned_at', 'unassigned_at', 'status'])
                    ->withTimestamps()
                    ->wherePivot('status', WorkerAssignmentStatus::ACTIVE->value);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
