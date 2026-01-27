<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case PRESENT = 'present';
    case ABSENT = 'absent';
    case SICK_LEAVE = 'sick_leave';
    case ANNUAL_LEAVE = 'annual_leave';
    case HOLIDAY = 'holiday';
}
