<?php

namespace App\Models;

use App\Enums\WorkerAssignmentStatus;
use Illuminate\Database\Eloquent\Model;

class ProjectWorker extends Model
{
    protected $casts = [
        'status' => WorkerAssignmentStatus::class,
    ];
}
