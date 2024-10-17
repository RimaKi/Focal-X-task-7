<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\StoreRequest;
use App\Models\Task;
use Illuminate\Support\Facades\Cache;

class CommentController extends Controller
{
    /**
     * @param StoreRequest $request
     * @param Task $task
     * @return string
     */
    public function store(StoreRequest $request, Task $task)
    {
        $data = $request->validationData();
        $data['user_id'] = auth()->user()->id;
        $task->comments()->create($data);
        Cache::forget("task_$task->id");
        return 'added successfully';
    }
}
