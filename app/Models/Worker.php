<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Worker extends Model
{
    protected $fillable = [
        'passport_number',
        'first_name',
        'last_name',
        'daily_rate',
        'ot_rate',
    ];

    protected $casts = [
        'daily_rate' => 'decimal:2',
        'ot_rate' => 'decimal:2',
    ];

    // helper for full name
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    // projects worker is assigned to
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_workers')
                    ->withPivot(['assigned_at', 'unassigned_at', 'status'])
                    ->withTimestamps();
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
