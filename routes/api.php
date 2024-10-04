<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TimesheetController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// User Routes
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/users', [UserController::class, 'store']); // Create a user
    Route::get('/users/{id}', [UserController::class, 'show']); // Get a user by ID
    Route::get('/users', [UserController::class, 'index']); // Get all users
    Route::put('/users/{user}', [UserController::class, 'update']); // Update a user
    Route::delete('/users/{user}', [UserController::class, 'destroy']); // Delete a user

    Route::post('/projects', [ProjectController::class, 'store']); // Create a project
    Route::get('/projects/{id}', [ProjectController::class, 'show']); // Get a project by ID
    Route::get('/projects', [ProjectController::class, 'index']); // Get all projects
    Route::put('/projects/{project}', [ProjectController::class, 'update']); // Update a project
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy']); // Delete a project

    Route::post('/timesheets', [TimesheetController::class, 'store']); // Create a timesheet
    Route::get('/timesheets/{id}', [TimesheetController::class, 'show']); // Get a timesheet by ID
    Route::get('/timesheets', [TimesheetController::class, 'index']); // Get all timesheets
    Route::put('/timesheets/{id}', [TimesheetController::class, 'update']); // Update a timesheet
    Route::delete('/timesheets/{id}', [TimesheetController::class, 'destroy']); // Delete a timesheet

});
