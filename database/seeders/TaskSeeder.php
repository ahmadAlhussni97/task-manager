<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create a test user
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
            ]
        );

        // Create sample tasks for the user
        $tasks = [
            [
                'title' => 'Complete project documentation',
                'description' => 'Write comprehensive documentation for the new feature implementation',
                'status' => 'in-progress',
                'due_date' => now()->addDays(3),
            ],
            [
                'title' => 'Review code changes',
                'description' => 'Review pull requests and provide feedback to team members',
                'status' => 'pending',
                'due_date' => now()->addDays(1),
            ],
            [
                'title' => 'Setup development environment',
                'description' => 'Install and configure all necessary tools and dependencies',
                'status' => 'completed',
                'due_date' => now()->subDays(2),
            ],
            [
                'title' => 'Plan team meeting agenda',
                'description' => 'Prepare agenda items and schedule for the weekly team meeting',
                'status' => 'pending',
                'due_date' => now()->addDays(5),
            ],
            [
                'title' => 'Fix critical bug in production',
                'description' => 'Investigate and resolve the reported critical bug affecting users',
                'status' => 'in-progress',
                'due_date' => now()->addHours(6),
            ],
            [
                'title' => 'Update user manual',
                'description' => 'Update the user manual with new features and improvements',
                'status' => 'pending',
                'due_date' => now()->addDays(7),
            ],
            [
                'title' => 'Backup database',
                'description' => 'Perform regular database backup and verify data integrity',
                'status' => 'completed',
                'due_date' => now()->subDays(1),
            ],
            [
                'title' => 'Optimize application performance',
                'description' => 'Analyze and improve application performance metrics',
                'status' => 'pending',
                'due_date' => now()->addDays(10),
            ],
        ];

        foreach ($tasks as $taskData) {
            Task::create(array_merge($taskData, ['user_id' => $user->id]));
        }

        // Create additional random tasks using factory
        Task::factory(20)->create(['user_id' => $user->id]);

        $this->command->info('Tasks seeded successfully!');
    }
} 