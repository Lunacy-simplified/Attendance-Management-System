<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    // GET /api/projects
    public function index(Request $request)
    {
        $user = $request->user();

        // superuser sees everything
        if ($user->role === 'superuser') {
            return response()->json([
                'status' => true,
                'data' => Project::all()
            ]);
        }

        // supervisor sees only thei assigned projects
        return response()->json([
            'status' => true,
            'data' => $user->projects
        ]);
    }

    // POST /api/projects/{id}/assign-supervisor
    public function assignSupervisor(Request $request, $id)
    {
        // security check
        if ($request->user()->role !== 'superuser') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $project = Project::findOrFail($id);

        // assign the supervisor
        // syncWithoutDetaching ensures we don't accidentally remove other supervisors
        $project->supervisors()->syncWithoutDetaching([
            $request->user_id => ['assigned_at' => now()]
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Supervisor assigned successfully',
        ]);
    }

    // GET /api/my-projects
    public function myProjects(Request $request)
    {
        $user = $request->user();

        // if superuser, return all projects
        if ($user->role === 'superuser') {
            return response()->json(Project::all());
        }

        // if supervisor, return only assigned projects
        return response()->json($user->projects);
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

    // GET /api/projects/{id}/workers
    public function getWorkers($id)
    {
        $project = Project::with('workers')->find($id);
        if (!$project) {
            return response()->json([
                'status' => false,
                'message' => 'Project not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $project->workers,
        ]);
    }
}
