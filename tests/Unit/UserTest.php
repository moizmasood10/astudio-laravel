<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class UserTest extends TestCase
{
    use RefreshDatabase; // Use this trait to refresh the database
    private $token;

    /** @test */
    public function it_can_create_a_user()
    {
        // First, create an authenticated user
        $this->authenticateUser();

        $response = $this->postJson('/api/users', [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'dob' => '1990-01-01',
            'gender' => 'female',
            'email' => 'janedoe@example.com',
            'password' => 'password123',
        ], [
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'janedoe@example.com']);
    }

    /** @test */
    public function it_can_get_a_user_by_id()
    {
        // First, create an authenticated user
        $this->authenticateUser();

        $user = User::factory()->create();

        $response = $this->getJson("/api/users/{$user->id}", [
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['email' => $user->email]);
    }

    /** @test */
    public function it_can_get_all_users()
    {
        // First, create an authenticated user
        $this->authenticateUser();

        User::factory()->count(3)->create();

        $response = $this->getJson('/api/users', [
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $response->assertStatus(200);
        $this->assertCount(4, $response->json());
    }

    /** @test */
    public function it_can_get_all_users_with_filters()
    {
        // First, create an authenticated user
        $this->authenticateUser();

        // Create 3 users with different attributes
        User::factory()->create([
            'first_name' => 'John',
            'gender' => 'male',
            'dob' => '1990-01-01',
        ]);

        User::factory()->create([
            'first_name' => 'Jane',
            'gender' => 'female',
            'dob' => '1995-05-10',
        ]);

        User::factory()->create([
            'first_name' => 'Mike',
            'gender' => 'male',
            'dob' => '1990-01-01',
        ]);

        // Create a user that shouldn't match the filters
        User::factory()->create([
            'first_name' => 'Alice',
            'gender' => 'female',
            'dob' => '1980-03-15',
        ]);

        // Send a GET request with filtering query parameters
        $response = $this->getJson('/api/users?first_name=John&gender=male&dob=1990-01-01', [
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        // Assert status is 200
        $response->assertStatus(200);

        // Assert only the matching user is returned
        $this->assertCount(1, $response->json());

        // Verify the correct user is returned in the response
        $response->assertJsonFragment([
            'first_name' => 'John',
            'gender' => 'male',
            'dob' => '1990-01-01',
        ]);

        // Check that the user who shouldn't match the filters is not present
        $response->assertJsonMissing([
            'first_name' => 'Alice',
        ]);
    }

    /** @test */
    public function it_can_update_a_user()
    {
        // First, create an authenticated user
        $this->authenticateUser();

        // Create a user to update
        $user = User::factory()->create();

        // Sending a new password along with password confirmation
        $response = $this->putJson('/api/users/' . $user->id, [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'dob' => '1990-01-01',
            'gender' => 'female',
            'email' => 'janedoe@example.com',
            'password' => 'newpassword123', // New password
            'password_confirmation' => 'newpassword123', // Add confirmation
        ], [
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        // Assert status is 200 and the user was updated in the database
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['email' => 'janedoe@example.com']);
    }


    /** @test */
    public function it_can_delete_a_user()
    {
        // First, create an authenticated user
        $this->authenticateUser();

        $user = User::factory()->create();

        // Send a DELETE request to the API
        $response = $this->deleteJson('/api/users/' . $user->id, [], [
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        // Assert the status is 200 and the user no longer exists in the database
        $response->assertStatus(200);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /** @test */
    public function it_requires_authentication_for_user_routes()
    {
        $response = $this->postJson('/api/users', [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'dob' => '1990-01-01',
            'gender' => 'female',
            'email' => 'janedoe@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(401); // Unauthorized
    }

    private function authenticateUser()
    {
        // Create a user
        $user = User::factory()->create([
            'password' => bcrypt('password123'), // Make sure the password is hashed
        ]);

        // Login to get an access token
        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        // Store the token
        $this->token = $response->json('token');
    }
}
