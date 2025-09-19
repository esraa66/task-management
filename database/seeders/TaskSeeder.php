<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Task;
use Database\Factories\TaskFactory;


class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $users = User::all();
        if ($users->count() == 0) return; 

    
        $tasks = Task::factory()->count(10)->make()->each(function ($task) use ($users) {
            $task->created_by = $users->random()->id;
            $task->assigned_to = $users->random()->id;
            $task->status = 'pending'; 
            $task->save();
        });

        
        $tasksArray = $tasks->all();
        foreach ($tasksArray as $task) {
            $dependencies = collect($tasksArray)
                ->where('id', '!=', $task->id) 
                ->random(rand(0, 3)); 
            foreach ($dependencies as $dep) {
                $task->dependencies()->attach($dep->id);
            }
        }
    }
}
