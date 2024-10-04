<?php

namespace Tests\Unit;

use App\Models\Timesheet;
use App\Models\User;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TimesheetTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_timesheet()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/timesheets', [
            'user_id' => $user->id,
            'project_id' => $project->id,
            'task_name' => 'Test Task',
            'date' => '2024-10-04',
            'hours' => 8,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('timesheets', ['task_name' => 'Test Task']);
    }

    public function test_can_show_timesheet()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();
        $timesheet = Timesheet::factory()->create(['user_id' => $user->id, 'project_id' => $project->id]);

        $response = $this->actingAs($user)->getJson('/api/timesheets/' . $timesheet->id);

        $response->assertStatus(200)
            ->assertJsonFragment(['task_name' => $timesheet->task_name]);
    }

    public function test_can_list_all_timesheets()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();
        Timesheet::factory()->count(3)->create(['user_id' => $user->id, 'project_id' => $project->id]);

        $response = $this->actingAs($user)->getJson('/api/timesheets');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_can_update_timesheet()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();
        $timesheet = Timesheet::factory()->create(['user_id' => $user->id, 'project_id' => $project->id]);

        $response = $this->actingAs($user)->putJson('/api/timesheets/' . $timesheet->id, [
            'task_name' => 'Updated Task',
            'date' => '2024-10-05',
            'hours' => 10,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('timesheets', ['task_name' => 'Updated Task']);
    }

    public function test_can_delete_timesheet()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();
        $timesheet = Timesheet::factory()->create(['user_id' => $user->id, 'project_id' => $project->id]);

        $response = $this->actingAs($user)->deleteJson('/api/timesheets/' . $timesheet->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('timesheets', ['id' => $timesheet->id]);
    }
}
