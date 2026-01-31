<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MonthlyAttendanceExport implements FromCollection, WithHeadings, WithMapping
{
    protected $month;
    protected $year;    

    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

    // get data
    public function collection()
    {
        return Attendance::with(['worker', 'project'])
            ->whereMonth('date', $this->month)
            ->whereYear('date', $this->year)
            ->get();
    }

    // define columns (headers)
    public function headings(): array
    {
        return [
            'Date',
            'Project Name',
            'Worker Name',
            'Designation',
            'Passport No',
            'Country',
            'Status',
            'OT Hours',
            'Daily Cost (MVR)',
            'OT Cost (MVR)',
            'Total Cost (MVR)',
        ];
    }

    // map data to columns
    public function map($attendance): array
    {
        // snapshot rate calculation
        $dailyCost = $attendance->salary_effective_rate;
        $otCost = $attendance->ot_hours * $attendance->ot_effective_rate;

        // safely get worker details
        $worker = $attendance->worker;

        return [
            $attendance->date,
            $attendance->project->name ?? 'N/A',
            $worker ? $worker->first_name . ' ' . $worker->last_name : 'N/A',
            $worker->designation ?? 'N/A',
            $worker->passport_number ?? 'N/A',
            $worker->country ?? 'N/A',
            $attendance->status->value ?? 'N/A',
            $attendance->ot_hours,
            number_format($dailyCost, 2),
            number_format($otCost, 2),
            number_format($dailyCost + $otCost, 2),
        ];

    }
}
