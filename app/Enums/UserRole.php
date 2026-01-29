<?php

namespace App\Enums;

enum UserRole: string
{
    case SUPERUSER = 'superuser';
    case SUPERVISOR = 'supervisor';
    case WORKER = 'worker';
}
