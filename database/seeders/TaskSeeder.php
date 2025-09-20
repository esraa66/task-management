<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Task;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $managers = User::role('manager')->get();
        $regularUsers = User::role('user')->get();

        if ($managers->isEmpty() || $regularUsers->isEmpty()) {
            $this->command->warn(' No managers or users found. Please seed users first.');
            return;
        }

        
        $tasks = collect();
        foreach (range(1, 10) as $i) {
            $tasks->push(Task::factory()->create([
                'created_by'   => $managers->random()->id,
                'assigned_to'  => $regularUsers->random()->id,
                'status'       => 'pending',
            ]));
        }

       
        foreach ($tasks as $task) {
            $dependencies = $tasks->where('id', '!=', $task->id)->random(rand(0, 3));
            foreach ($dependencies as $dep) {
              
                if (!$task->dependencies->contains($dep->id)) {
                    $task->dependencies()->attach($dep->id);
                }
            }
        }
    }
}
