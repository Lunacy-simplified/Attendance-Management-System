<?php

namespace App\Models;

use App\Enums\ProjectStatus;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $casts = [
        'status' => ProjectStatus::class,
    ];
}
