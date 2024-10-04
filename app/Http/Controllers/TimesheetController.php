<?php

namespace App\Http\Controllers;

use App\Models\Timesheet;
use Illuminate\Http\Request;

class TimesheetController extends Controller
{
    // Store a new timesheet
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'project_id' => 'required|exists:projects,id',
            'task_name' => 'required|string|max:255',
            'date' => 'required|date',
            'hours' => 'required|integer|min:1',
        ]);

        $timesheet = Timesheet::create($validatedData);
        return response()->json($timesheet, 201);
    }

    // Show a specific timesheet
    public function show($id)
    {
        $timesheet = Timesheet::with(['user', 'project'])->findOrFail($id);
        return response()->json($timesheet);
    }

    // List all timesheets
    public function index()
    {
        $timesheets = Timesheet::with(['user', 'project'])->get();
        return response()->json($timesheets);
    }

    // Update an existing timesheet
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'task_name' => 'sometimes|required|string|max:255',
            'date' => 'sometimes|required|date',
            'hours' => 'sometimes|required|integer|min:1',
        ]);

        $timesheet = Timesheet::findOrFail($id);
        $timesheet->update($validatedData);

        return response()->json($timesheet);
    }

    // Delete a timesheet
    public function destroy($id)
    {
        $timesheet = Timesheet::findOrFail($id);
        $timesheet->delete();

        return response()->json(null, 204);
    }
}
