<?php

namespace App\Models;

use App\Enums\AttendanceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';

    protected $fillable = [
        'worker_id',
        'project_id',
        'user_id',
        'date',
        'status',
        'ot_hours',
        'salary_effective_rate',
        'ot_effective_rate',
    ];

    protected $casts = [
        'status' => AttendanceStatus::class,
        'date' => 'date',
        'ot_hours' => 'decimal:2',
        'salary_effective_rate' => 'decimal:2',
        'ot_effective_rate' => 'decimal:2',
    ];

    // Relationships
    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    // supervisor who entered this record
    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // calculation helper
    // This allows us to call $attendance->total_play anywhere in your app
    public function getTotalPayAttribute()
    {
        $basePay = 0;
        $otPay = 0;

        // Logic: If Present, calculate Daily Rate
        if ($this->status === AttendanceStatus::PRESENT) {
            $basePay = $this->salary_effective_rate;
        }

        // OT Calculation
        if ($this->ot_hours > 0) {
            $otPay = $this->ot_hours * $this->ot_effective_rate;
        }

        return $basePay + $otPay;
    }
}
