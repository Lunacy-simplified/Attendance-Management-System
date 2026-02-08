<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Worker;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    // GET /api/attendance
    public function index(Request $request)
    {
        $query = Attendance::with('worker');

        // filter by date (default to today)
        if ($request->has('date')) {
            $query->whereDate('date', $request->date);
        }

        // filter by project
        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        return response()->json($query->latest()->get());
    }

    // POST /api/attendance
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.worker_id' => 'required|exists:workers,id',
            'attendance.*.status' => 'required|string',
            'attendance.*.ot_hours' => 'nullable|numeric|min:0',
        ]);

        $records = [];

        foreach ($validated['attendance'] as $item) {
            // Look up worker to get the snapshot rates
            $worker = Worker::findOrFail($item['worker_id']);

            $records[] = Attendance::updateOrCreate(
                [
                    'project_id' => $validated['project_id'],
                    'worker_id' => $item['worker_id'],
                    'date' => $validated['date'],
                ],
                [
                    'user_id' => auth()->id(),
                    'status' => $item['status'],
                    'ot_hours' => $item['ot_hours'] ?? 0,
                    'salary_effective_rate' => $worker->daily_rate,
                    'ot_effective_rate' => $worker->ot_rate,
                ]
            );
        }

        return response()->json([
            'status' => true,
            'message' => count($records).' attendance records processed.',
            'data' => $records,
        ], 201);
    }
}
