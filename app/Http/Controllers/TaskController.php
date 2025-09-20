<?php
namespace App\Http\Controllers;

use App\Models\Task;
use App\Services\TaskService;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Requests\AssignTaskRequest;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class TaskController extends Controller
{
    protected $service;
    use AuthorizesRequests;

    public function __construct(TaskService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Task::class);

        $filters = $request->only(['status', 'due_date_from', 'due_date_to', 'due_date', 'assigned_to']);
        $tasks = $this->service->listTasks($filters);

        return $this->success($tasks, 'Tasks retrieved successfully');
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);

        $taskDetails = $this->service->getTask($task->id);

        return $this->success($taskDetails, 'Task details retrieved successfully');
    }

    public function store(StoreTaskRequest $request)
    {
        $this->authorize('create', Task::class);

        $task = $this->service->createTask($request->validated());

        return $this->success($task, 'Task created successfully', 201);
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);

        $task = $this->service->updateTask($task, $request->validated());

        $statusMessage = match ($task->status) {
            'pending'   => 'Task marked as pending.',
            'in_progress' => 'Task is now in progress.',
            'completed' => 'Task completed successfully.',
            default     => 'Task status updated.',
        };

        return $this->success($task, $statusMessage);
    }

    public function assign(AssignTaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);

        $task = $this->service->assignTask($task, $request->validated()['user_id']);

        return $this->success($task, 'Task assigned successfully');
    }

    public function addDependency(Request $request, Task $task)
    {
        $this->authorize('manageDependencies', $task);

        $request->validate([
            'dependency_id' => 'required|integer|exists:tasks,id'
        ]);

        $task = $this->service->addDependency($task, $request->dependency_id);

        return $this->success($task, 'Dependency added successfully');
    }

    public function removeDependency(Request $request, Task $task)
    {
        $this->authorize('manageDependencies', $task);

        $request->validate([
            'dependency_id' => 'required|integer|exists:tasks,id'
        ]);

        $task = $this->service->removeDependency($task, $request->dependency_id);

        return $this->success($task, 'Dependency removed successfully');
    }
}
