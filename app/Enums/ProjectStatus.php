<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case ACTIVE = 'active';
    case HALTED = 'halted';
    case COMPLETED = 'completed';
}
