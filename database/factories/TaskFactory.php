<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = ['pending', 'in-progress', 'completed'];
        
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(3, 6),
            'description' => $this->faker->optional(0.7)->paragraph(2, 4),
            'status' => $this->faker->randomElement($statuses),
            'due_date' => $this->faker->optional(0.6)->dateTimeBetween('now', '+30 days'),
            'tags' => $this->faker->optional(0.8)->randomElements([
                'urgent', 'important', 'work', 'personal', 'meeting', 'deadline', 
                'project', 'review', 'follow-up', 'planning', 'development', 'testing'
            ], $this->faker->numberBetween(1, 3)),
        ];
    }

    /**
     * Indicate that the task is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the task is in progress.
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'in-progress',
        ]);
    }

    /**
     * Indicate that the task is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    /**
     * Indicate that the task is overdue.
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'due_date' => $this->faker->dateTimeBetween('-30 days', '-1 day'),
            'status' => $this->faker->randomElement(['pending', 'in-progress']),
        ]);
    }

    /**
     * Indicate that the task is due today.
     */
    public function dueToday(): static
    {
        return $this->state(fn (array $attributes) => [
            'due_date' => now()->toDateString(),
        ]);
    }
} 