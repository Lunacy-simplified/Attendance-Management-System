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
        if ($request->has('date')) {
            $query->where('project_id', $request->project_id);
        }

        return response()->json($query->latest()->get());
    }

    // POST /api/attendance
    public function store(Request $request)
    {
        $validated = $request->validate([
            'worker_id' => 'required|exists:workers,id',
            'project_id' => 'required|exists:projects,id',
            'date' => 'required|date',
            'status' => ['required'],
            'ot_hours' => 'nullable|numeric|min:0',
        ]);

        // security to look up the real rates from the db
        $worker = Worker::findorFail($validated['worker_id']);

        // create attendance record
        $attendance = Attendance::create([
            'worker_id' => $validated['worker_id'],
            'project_id' => $validated['project_id'],
            'user_id' => auth()->id(),
            'date' => $validated['date'],
            'status' => $validated['status'],
            'ot_hours' => $validated['ot_hours'] ?? 0,

            // snapshotting, save the rates as they are rn
            'salary_effective_rate' => $worker->daily_rate,
            'ot_effective_rate' => $worker->ot_rate,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Attendance record created successfully',
            'data' => $attendance,
        ], 201);
    }
}
