<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'due_date',
        'assigned_to',
        'created_by',
    ];

   
    // User assigned to the task.
   
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

   
    // User who created the task.
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

  
    // Tasks this task depends on.
    
    public function dependencies()
    {
        return $this->belongsToMany(
            Task::class,
            'task_dependencies',
            'task_id',             
            'dependency_task_id'   
        );
    }

    
    // Tasks that depend on this task.
    
    public function dependents()
    {
        return $this->belongsToMany(
            Task::class,
            'task_dependencies',
            'dependency_task_id',  
            'task_id'              
        );
    }
}
