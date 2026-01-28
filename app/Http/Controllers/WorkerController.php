<?php

namespace App\Http\Controllers;

use App\Models\Worker;
use Illuminate\Http\Request;

class WorkerController extends Controller
{
    // GET /api/workers
    public function index()
    {
        $workers = Worker::all();

        return response()->json([
            'status' => true,
            'data' => $workers,
        ]);
    }

    // POST /api/workers
    public function store(Request $request)
    {
        // Validate incoming data
        $validated = $request->validate([
            'passport_number' => 'required|string|unique:workers,passport_number',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'daily_rate' => 'required|numeric|min:0',
            'ot_rate' => 'required|numeric|min:0',
        ]);

        // create worker in db
        $worker = Worker::create($validated);

        // return response as json
        return response()->json([
            'status' => true,
            'message' => 'Worker created successfully',
            'data' => $worker,
        ], 201);
    }

    
}
