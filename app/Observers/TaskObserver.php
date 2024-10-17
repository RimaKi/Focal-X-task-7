<?php

namespace App\Observers;

use App\Models\Task;
use App\Models\TaskStatusUpdate;

class TaskObserver
{
    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void
    {
        TaskStatusUpdate::create([
            'task_id' => $task->id,
            'current_status' => $task->status,
            'description' => "Added task"
        ]);
    }

    /**
     * Handle the Task "updated" event.
     */
    public function updated(Task $task): void
    {
        TaskStatusUpdate::create([
            'task_id' => $task->id,
            'current_status' => $task->status,
            'description' => "Updated task"
        ]);
        if ($task->status == 'completed') {
            foreach ($task->dependentTasks()->get() as $dependentTask) {
                Task::find($dependentTask->task_id)->updateStatusBasedOnDependencies();
            }
        }
    }

    /**
     * Handle the Task "deleted" event.
     */
    public function deleted(Task $task): void
    {
        TaskStatusUpdate::create([
            'task_id' => $task->id,
            'current_status' => $task->status,
            'description' => "Soft deleted task"
        ]);
    }

    /**
     * Handle the Task "restored" event.
     */
    public function restored(Task $task): void
    {
        TaskStatusUpdate::create([
            'task_id' => $task->id,
            'current_status' => $task->status,
            'description' => "Restored task"
        ]);
    }

    /**
     * Handle the Task "force deleted" event.
     */
    public function forceDeleted(Task $task): void
    {
        TaskStatusUpdate::create([
            'task_id' => $task->id,
            'current_status' => $task->status,
            'description' => "ForceDeleted task"
        ]);
    }
}
