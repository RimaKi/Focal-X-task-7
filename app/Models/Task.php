<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'type',
        'status',
        'priority',
        'due_date',
        'assigned_to'
    ];

    public function assigned_to()
    {
        return $this->belongsTo(User::class, 'assigned_to', 'id');
    }

    public function taskStatusUpdates()
    {
        return $this->hasMany(TaskStatusUpdate::class);
    }

    // المهام التي تعتمد على هذه المهمة
    public function dependentTasks()
    {
        return $this->hasMany(TaskDependency::class, 'depends_on_task_id');
    }

    // المهام التي تعتمد عليها المهمة
    public function dependencies()
    {
        return $this->hasMany(TaskDependency::class, 'task_id');
    }

    // التحقق من أن جميع المهام التي تعتمد عليها مكتملة
    public function checkDependenciesCompleted()
    {
        foreach ($this->dependencies as $dependency) {
            if ($dependency->dependsOnTask->status != 'completed') {
                return false;
            }
        }
        return true;
    }

    // تغيير حالة المهمة بناءً على الاعتماديات
    public function updateStatusBasedOnDependencies()
    {
        if ($this->checkDependenciesCompleted() && $this->status == 'blocked') {
            $this->status = 'open';
            $this->save();
        }
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }


}
