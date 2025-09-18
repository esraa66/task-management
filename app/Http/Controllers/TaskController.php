<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Requests\AssignTaskRequest;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Task::class);

        $user = auth()->user();

        if ($user->hasRole('Manager')) {
            $tasks = Task::latest()->get();
        } else {
            $tasks = Task::where('assigned_to', $user->id)->latest()->get();
        }

        return response()->json($tasks);
    }

    public function store(StoreTaskRequest $request)
    {
        $this->authorize('create', Task::class);

        $data = $request->validated();
        $data['status'] = 'pending';
        $data['created_by'] = auth()->id();

        $task = Task::create($data);

        return response()->json($task, 201);
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $user = auth()->user();

        
        if ($user->hasRole('Manager')) {
            $this->authorize('update', $task);
            $task->update($request->validated());
            return response()->json($task);
        }

   
        $this->authorize('updateStatus', $task);

        $data = $request->only('status');
        $task->update($data);

        return response()->json($task);
    }

    public function assign(AssignTaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);

        $task->update(['assigned_to' => $request->validated()['user_id']]);

        return response()->json($task);
    }
}
