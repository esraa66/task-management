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

        return response()->json([
            'data' => $tasks,
            'meta' => [
                'total' => $tasks->count(),
                'filters' => $filters
            ]
        ]);
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);

        $taskDetails = $this->service->getTask($task->id);

        return response()->json([
            'data' => $taskDetails
        ]);
    }

    public function store(StoreTaskRequest $request)
    {
        $this->authorize('create', Task::class);

        $task = $this->service->createTask($request->validated());

        return response()->json([
            'data' => $task,
            'message' => 'Task created successfully'
        ], 201);
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);

        $task = $this->service->updateTask($task, $request->validated());

        return response()->json([
            'data' => $task,
            'message' => 'Task updated successfully'
        ]);
    }

    public function assign(AssignTaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);

        $task = $this->service->assignTask($task, $request->validated()['user_id']);

        return response()->json([
            'data' => $task,
            'message' => 'Task assigned successfully'
        ]);
    }

    public function addDependency(Request $request, Task $task)
    {
        $this->authorize('manageDependencies', $task);

        $request->validate([
            'dependency_id' => 'required|integer|exists:tasks,id'
        ]);

        $task = $this->service->addDependency($task, $request->dependency_id);

        return response()->json([
            'data' => $task,
            'message' => 'Dependency added successfully'
        ]);
    }

    public function removeDependency(Request $request, Task $task)
    {
        $this->authorize('manageDependencies', $task);

        $request->validate([
            'dependency_id' => 'required|integer|exists:tasks,id'
        ]);

        $task = $this->service->removeDependency($task, $request->dependency_id);

        return response()->json([
            'data' => $task,
            'message' => 'Dependency removed successfully'
        ]);
    }
}
