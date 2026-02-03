<?php

namespace App\Http\Controllers;

use App\Exports\MonthlyAttendanceExport;
use App\Models\Attendance;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
class ReportController extends Controller
{
    // GET /api/reports/attendance?month=1&year=2026
    public function export(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2026|max:2100',
        ]);

        $filename = 'attendance_report_' . $request->year . '_' . $request->month . '.xlsx';

        return Excel::download(
            new MonthlyAttendanceExport($request->month, $request->year),
            $filename
        );
    }

    public function monthly(Request $request)
    {
        // 1. Get Filters
        $projectId = $request->project_id; // Standardized variable name
        $month = $request->month ?? now()->format('Y-m');

        if (!$projectId) {
            return response()->json(['message' => 'Project ID required'], 400);
        }

        // 2. Calculate Date Range
        $startOfMonth = Carbon::parse($month)->startOfMonth();
        $endOfMonth = Carbon::parse($month)->endOfMonth();
        $daysInMonth = $startOfMonth->daysInMonth;

        // 3. Get Workers
        $project = Project::with(['workers' => function($q) {
            $q->orderBy('first_name');
        }])->find($projectId);

        if (!$project) {
            return response()->json(['message' => 'Project not found'], 404);
        }

        // 4. Fetch Attendance (Fixed variable name mismatch: $projectID vs $projectId)
        $attendanceData = Attendance::where('project_id', $projectId)
            ->whereBetween('date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->get()
            ->groupBy('worker_id');

        // 5. Build Report
        $report = [];

        foreach ($project->workers as $worker) {
            $workerAttendance = $attendanceData->get($worker->id, collect());
            
            $days = [];

            $stats = [
                'present' => 0, 
                'absent' => 0, 
                'sick_leave' => 0, 
                'holiday' => 0, 
                'ot_hours' => 0
            ];

            for ($i = 1; $i <= $daysInMonth; $i++) {
                $currentDate = $startOfMonth->copy()->day($i)->format('Y-m-d');
                $record = $workerAttendance->first(function($att) use ($currentDate) {
                    return Carbon::parse($att->date)->format('Y-m-d') === $currentDate;
                });

                if ($record) {
                    $statusValue = $record->status instanceof \App\Enums\AttendanceStatus 
                                    ? $record->status->value 
                                    : $record->status;

                    $days[$i] = [
                        'status' => $statusValue,
                        'ot' => $record->ot_hours
                    ];
                    

                    if (isset($stats[$statusValue])) {
                        $stats[$statusValue]++;
                    }
                    
                    $stats['ot_hours'] += $record->ot_hours;

                } else {
                    $days[$i] = null; 
                }
            }

            $report[] = [
                'worker' => $worker->first_name . ' ' . $worker->last_name,
                'days' => $days,
                'stats' => $stats
            ];
        }

        return response()->json([
            'project_name' => $project->name,
            'month_label' => $startOfMonth->format('F Y'),
            'total_days' => $daysInMonth,
            'data' => $report
        ]);
    }

}
