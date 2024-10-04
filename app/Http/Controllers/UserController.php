<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Create a new user
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'dob' => 'required|date',
            'gender' => 'required|string|max:10',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6'
        ]);

        $validatedData['password'] = Hash::make($validatedData['password']);

        $user = User::create($validatedData);

        return response()->json($user, 201);
    }

    // Get a User
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    // Get all users
    public function index(Request $request)
    {
        // Get the filter values from the request
        $firstName = $request->input('first_name');
        $gender = $request->input('gender');
        $dob = $request->input('dob');

        // Start the query on the User model
        $query = User::query();

        // Apply filters if they are provided
        if ($firstName) {
            $query->where('first_name', 'LIKE', '%' . $firstName . '%');
        }
        if ($gender) {
            $query->where('gender', $gender);
        }
        if ($dob) {
            $query->whereDate('dob', $dob);
        }

        // Execute the query to get the filtered users
        $users = $query->get();

        // Return the users as a JSON response
        return response()->json($users);
    }

    // Update a user
    public function update(Request $request, User $user)
    {
        // Validate the request
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'dob' => 'required|date',
            'gender' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $user->id, // unique except for current user
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Update the user with validated data
        if ($request->filled('password')) {
            $validatedData['password'] = bcrypt($validatedData['password']);
        } else {
            unset($validatedData['password']);
        }

        $user->update($validatedData);

        // Return a response indicating success
        return response()->json(['message' => 'User updated successfully'], 200);
    }

    // Delete a user
    public function destroy(User $user)
    {
        // Delete the user
        $user->delete();

        // Return a response indicating success
        return response()->json(['message' => 'User deleted successfully'], 200);
    }

}
