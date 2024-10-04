<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    // Method to retrieve a list of projects with their associated users
    public function index(): JsonResponse
    {
        // Retrieve all projects with associated users
        $projects = Project::with('users')->get();

        // Return the projects as a JSON response
        return response()->json($projects, 200);
    }

    // Store a newly created project in storage
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'dept' => 'required|string|max:255', // Validation rule for dept
            'start_date' => 'required|date', // Validation rule for start_date
            'end_date' => 'nullable|date|after_or_equal:start_date', // Validation rule for end_date
            'status' => 'required|string|max:255', // New validation rule for status
        ]);

        // Create the project without associating the user yet
        $project = Project::create([
            'name' => $request->name,
            'dept' => $request->dept, // Include dept in project creation
            'start_date' => $request->start_date, // Include start_date in project creation
            'end_date' => $request->end_date, // Include end_date in project creation
            'status' => $request->status, // Include status in project creation
        ]);

        // Attach the authenticated user to the project via the pivot table
        $project->users()->attach(Auth::id());

        return response()->json($project->load('users'), 201); // Load users to return with the project
    }

    // Display the specified project
    public function show($id): JsonResponse
    {
        // Retrieve the project by ID
        $project = Project::with('users')->find($id);

        // Check if the project exists
        if (!$project) {
            return response()->json(['message' => 'Project not found'], 404);
        }

        // Return the project data with associated users
        return response()->json($project, 200);
    }

    // Update the specified project in storage
    // Method to update a specific project
    public function update(Request $request, Project $project): JsonResponse
    {
        // Validate the request data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'dept' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|string',
            'users' => 'nullable|array', // Optional: If you want to update the associated users
            'users.*' => 'exists:users,id',
        ]);

        // Update project fields
        $project->update($validated);

        // Optionally update users associated with the project
        if (isset($validated['users'])) {
            $project->users()->sync($validated['users']); // Sync users if provided
        }

        // Return the updated project along with associated users
        return response()->json($project->load('users'), 200);
    }

    // Remove the specified project from storage
    public function destroy(Project $project)
    {
        // Optionally, you can add authorization logic here to check if the user can delete the project.

        // Delete the project
        $project->delete();

        // Return a response indicating successful deletion
        return response()->json(['message' => 'Project deleted successfully'], 200);
    }

}
