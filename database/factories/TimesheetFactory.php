<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Project;
use App\Models\Timesheet;
use App\Models\User;

class TimesheetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Timesheet::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'task_name' => $this->faker->word(),
            'date' => $this->faker->date(),
            'hours' => $this->faker->randomNumber(),
            'project_id' => Project::factory(),
            'user_id' => User::factory(),
        ];
    }
}
