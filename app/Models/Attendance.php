<?php

namespace App\Models;

use App\Enums\AttendanceStatus;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $casts = [
        'status' => AttendanceStatus::class,
    ];
}
