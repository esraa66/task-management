<?php

namespace App\Services;

use App\Models\Task;
use App\Repositories\TaskRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TaskService
{
    protected $repo;

    public function __construct(TaskRepository $repo)
    {
        $this->repo = $repo;
    }

    public function listTasks(array $filters = [])
    {
        return $this->repo->all($filters);
    }

    public function createTask(array $data): Task
    {
        // Manager only
        if (!Auth::user()->hasRole('Manager')) {
            throw ValidationException::withMessages([
                'permission' => 'Only managers can create tasks.'
            ]);
        }

        $data['created_by'] = Auth::id();
        return $this->repo->create($data);
    }

    public function updateTask(Task $task, array $data): Task
    {
        // Manager only
        if (!Auth::user()->hasRole('Manager')) {
            throw ValidationException::withMessages([
                'permission' => 'Only managers can update tasks.'
            ]);
        }

        return $this->repo->update($task, $data);
    }

    public function assignTask(Task $task, int $userId): Task
    {
        if (!Auth::user()->hasRole('Manager')) {
            throw ValidationException::withMessages([
                'permission' => 'Only managers can assign tasks.'
            ]);
        }

        return $this->repo->update($task, ['assigned_to' => $userId]);
    }
}
