<?php

namespace App\Services;

use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Traits\AuthorizesRoles;



class TaskService
{

    use AuthorizesRoles;
    protected TaskRepositoryInterface $repo;

    public function __construct(TaskRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }
    
   

    
    public function listTasks(array $filters = [])
    {
        $user = Auth::user();

        if (!$user->hasRole('manager')) {
            $filters['assigned_to'] = $user->id;
        }

        return $this->repo->all($filters);
    }

   
    public function getTask(int $id): Task
    {
        $task = $this->repo->find($id);

        if (!$task) {
            throw ValidationException::withMessages([
                'task' => "Task with ID {$id} not found."
            ]);
        }

        return $task;
    }

    
    public function addDependency(Task $task, int $dependencyId): Task
    {
        $this->authorizeManagerOnly();

        if ($this->wouldCreateCircularDependency($task, $dependencyId)) {
            throw ValidationException::withMessages([
                'dependency' => 'Circular dependency detected.'
            ]);
        }

        $task->dependencies()->attach($dependencyId);
        return $task->fresh(['dependencies', 'dependents']);
    }

   
    public function removeDependency(Task $task, int $dependencyId): Task
    {
        $this->authorizeManagerOnly();

        $task->dependencies()->detach($dependencyId);
        return $task->fresh(['dependencies', 'dependents']);
    }

    
    private function wouldCreateCircularDependency(Task $task, int $dependencyId): bool
    {
        $dependencyTask = Task::find($dependencyId);
        if (!$dependencyTask) {
            return false;
        }

        return in_array($task->id, $this->getAllDependencies($dependencyTask));
    }

    private function getAllDependencies(Task $task, array &$visited = []): array
    {
        if (in_array($task->id, $visited)) {
            return [];
        }

        $visited[] = $task->id;
        $dependencies = [];

        foreach ($task->dependencies as $dependency) {
            $dependencies[] = $dependency->id;
            $dependencies = array_merge(
                $dependencies,
                $this->getAllDependencies($dependency, $visited)
            );
        }

        return $dependencies;
    }

    
    public function createTask(array $data): Task
    {
        $this->authorizeManagerOnly();

        $data['status'] = 'pending';
        $data['created_by'] = Auth::id();
        $data['assigned_to'] = $data['assigned_to'] ?? null;

        return $this->repo->create($data);
    }

   
    public function updateTask(Task $task, array $data): Task
    {
        $user = Auth::user();

        if (!$user->hasRole('manager')) {
            if ($task->assigned_to !== $user->id) {
                throw ValidationException::withMessages([
                    'permission' => 'You can only update your assigned tasks.'
                ]);
            }

            $allowedFields = ['status'];
            $data = array_intersect_key($data, array_flip($allowedFields));

            if (($data['status'] ?? null) === 'completed') {
                $this->validateDependenciesCompleted($task);
            }
        } else {
            if (($data['status'] ?? null) === 'completed') {
                $this->validateDependenciesCompleted($task);
            }
        }

        return $this->repo->update($task, $data);
    }

   
    public function assignTask(Task $task, int $userId): Task
    {
        $this->authorizeManagerOnly();

        return $this->repo->update($task, ['assigned_to' => $userId]);
    }

   
    private function validateDependenciesCompleted(Task $task): void
    {
        $incomplete = $task->dependencies()
            ->where('status', '!=', 'completed')
            ->pluck('id')
            ->toArray();

        if (!empty($incomplete)) {
            throw ValidationException::withMessages([
                'dependencies' => 'Cannot complete task. Incomplete dependencies: ' . implode(', ', $incomplete)
            ]);
        }
    }
}
