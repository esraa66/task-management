<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskDependency extends Model
{
    protected $table = 'task_dependencies';

    protected $fillable = [
        'task_id',
        'dependency_task_id',
    ];

    /**
     * The task that has a dependency.
     */
    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    /**
     * The task this task depends on.
     */
    public function dependencyTask()
    {
        return $this->belongsTo(Task::class, 'dependency_task_id');
    }
}