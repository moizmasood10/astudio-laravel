<?php

namespace Tests\Unit;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_project()
    {
        // Authenticate user
        $user = $this->authenticateUser(); // Assuming this method handles user authentication and returns the user

        $data = [
            'name' => 'Test Project',
            'dept' => 'Some Dept', // Add the dept field
            'start_date' => '2024-10-01', // Add the start_date field
            'end_date' => '2024-10-15', // Add the end_date field
            'status' => 'active', // Add the status field
        ];

        // Send the request with authentication
        $response = $this->actingAs($user)->postJson('/api/projects', $data);

        // Assert the response status
        $response->assertStatus(201);

        // Assert the project was created in the database
        $this->assertDatabaseHas('projects', [
            'name' => 'Test Project',
            'dept' => 'Some Dept', // Assert dept
            'start_date' => '2024-10-01 00:00:00', // Adjust start_date to include timestamp
            'end_date' => '2024-10-15 00:00:00', // Adjust end_date to include timestamp
            'status' => 'active', // Assert status
        ]);

        // Retrieve the project and verify that the user is attached to it
        $project = Project::where('name', 'Test Project')->first();

        // Check that the user is in the users collection of the project
        $this->assertTrue($project->users->contains($user->id));

        // Assert that the user is correctly attached in the pivot table
        $this->assertDatabaseHas('project_user', [
            'project_id' => $project->id,
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function it_can_show_a_project_with_users()
    {
        // Create a user and a project using factories
        $user = User::factory()->create();
        $project = Project::factory()->create();

        // Attach the user to the project
        $project->users()->attach($user);

        // Act as the created user
        $response = $this->actingAs($user)->getJson('/api/projects/' . $project->id);

        // Assert the response status and structure
        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'dept',
                'start_date',
                'end_date',
                'status',
                'users' => [
                    '*' => [ // Check each user structure
                        'id',
                        'first_name',
                        'last_name',
                        'dob',
                        'gender',
                        'email',
                        'pivot' => [ // Check pivot data if necessary
                            'project_id',
                            'user_id',
                        ],
                    ],
                ],
            ]);

        // Assert that the project details are correct
        $response->assertJsonFragment([
            'name' => $project->name,
            'dept' => $project->dept,
            'status' => $project->status,
        ]);

        // Assert that the user details are correct
        $response->assertJsonFragment([
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
        ]);
    }

    /** @test */
    public function it_can_list_all_projects_with_users()
    {
        // Create users and projects using factories
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $project1 = Project::factory()->create();
        $project2 = Project::factory()->create();

        // Attach users to projects
        $project1->users()->attach($user1);
        $project2->users()->attach($user2);

        // Act as a user (you can also create a separate user for this purpose)
        $response = $this->actingAs($user1)->getJson('/api/projects');

        // Assert the response status
        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [  // Expecting an array of projects
                    'id',
                    'name',
                    'dept',
                    'start_date',
                    'end_date',
                    'status',
                    'users' => [
                        '*' => [  // Each user within the project
                            'id',
                            'first_name',
                            'last_name',
                            'dob',
                            'gender',
                            'email',
                            'pivot' => [  // Pivot structure if necessary
                                'project_id',
                                'user_id',
                            ],
                        ],
                    ],
                ],
            ]);

        // Assert that the response contains the created projects and users by matching specific user details
        $response->assertJsonFragment([
            'id' => $project1->id,
            'name' => $project1->name,
        ]);

        $response->assertJsonFragment([
            'id' => $user1->id,
            'first_name' => $user1->first_name,
            'last_name' => $user1->last_name,
            'email' => $user1->email,
        ]);

        $response->assertJsonFragment([
            'id' => $project2->id,
            'name' => $project2->name,
        ]);

        $response->assertJsonFragment([
            'id' => $user2->id,
            'first_name' => $user2->first_name,
            'last_name' => $user2->last_name,
            'email' => $user2->email,
        ]);
    }
//
    /** @test */
    public function it_can_update_a_project()
    {
        // Create a user and a project using factories
        $user = User::factory()->create();
        $project = Project::factory()->create();

        // Attach the user to the project
        $project->users()->attach($user);

        // New data to update the project with
        $updatedData = [
            'name' => 'Updated Project Name',
            'dept' => 'Updated Department',
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31',
            'status' => 'in-progress',
            'users' => [$user->id], // Optionally updating users
        ];

        // Act as the authenticated user and send a PUT request to update the project
        $response = $this->actingAs($user)->putJson('/api/projects/' . $project->id, $updatedData);

        // Assert the response status and structure
        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'dept',
                'start_date',
                'end_date',
                'status',
                'users' => [
                    '*' => [  // Each user within the project
                        'id',
                        'first_name',
                        'last_name',
                        'email',
                    ],
                ],
            ]);

        // Assert that the project was updated correctly
        $response->assertJsonFragment([
            'name' => 'Updated Project Name',
            'dept' => 'Updated Department',
            'status' => 'in-progress',
        ]);

        // Assert that the project is still associated with the user
        $response->assertJsonFragment([
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
        ]);

        // Reload the project from the database and verify the update
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Updated Project Name',
            'dept' => 'Updated Department',
            'start_date' => '2024-01-01 00:00:00', // Add time to match database format
            'end_date' => '2024-12-31 00:00:00',   // Add time to match database format
            'status' => 'in-progress',
        ]);

        // Verify that the user association is correct in the pivot table
        $this->assertDatabaseHas('project_user', [
            'project_id' => $project->id,
            'user_id' => $user->id,
        ]);
    }
//
    /** @test */
    public function it_can_delete_a_project()
    {
        // Create a user
        $user = User::factory()->create();

        // Create a project
        $project = Project::factory()->create();

        // Act as the authenticated user and send a DELETE request to delete the project
        $response = $this->actingAs($user)->deleteJson('/api/projects/' . $project->id);

        // Assert the response status is 200 (OK)
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Project deleted successfully',
            ]);

        // Verify that the project no longer exists in the database
        $this->assertDatabaseMissing('projects', [
            'id' => $project->id,
        ]);
    }

    /** @test */
    private function authenticateUser()
    {
        // Create a user
        $user = User::factory()->create([
            'password' => bcrypt('password123'), // Ensure the password is hashed
        ]);

        // Authenticate the user directly using Sanctum
        $this->actingAs($user); // Set the authenticated user context for the test

        return $user; // Return the created user
    }

}
