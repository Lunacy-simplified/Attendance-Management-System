<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    // GET /api/projects
    public function index()
    {
        return response()->json([
            'status' => true,
            'data' => [Project::all()],
        ]);
    }

    
    // POST /api/projects
    public function store(Request $request)
    {
        // validate request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'status' => 'required|string|in:active,halted,completed',
        ]);

        // add project to db
        $project = Project::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Project created successfully',
            'data' => $project,
        ], 201);
    }

    // POST /api/projects/{id}/assign
    public function assignWorker(Request $request, $id)
    {
        // validate request
        $validated = $request->validate([
            'worker_id' => 'required|exists:workers,id',
        ]);

        // find project
        $project = Project::find($id);
        if (!$project) {
            return response()->json([
                'status' => false,
                'message' => 'Project not found',
            ], 404);
        }
        
        // attach the worker
        $project->workers()->syncWithoutDetaching([$validated['worker_id']]);

        return response()->json([
            'status' => true,
            'message' => 'Worker assigned to project successfully',
        ]);
    }
}
