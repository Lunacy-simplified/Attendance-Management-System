<?php

namespace App\Http\Controllers;

use App\Exports\MonthlyAttendanceExport;
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
}
