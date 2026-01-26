<?php

namespace App\Enums;

enum WorkerAssignmentStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}
