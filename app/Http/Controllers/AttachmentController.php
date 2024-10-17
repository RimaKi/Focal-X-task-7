<?php

namespace App\Http\Controllers;

use App\Http\Requests\Attachment\StoreRequest;
use App\Models\Task;
use App\Services\assetsService;
use Illuminate\Support\Facades\Cache;

class AttachmentController extends Controller
{
    /**
     * @param StoreRequest $request
     * @param Task $task
     * @return string
     * @throws \Exception
     */
    public function store(StoreRequest $request, Task $task)
    {
        $data = $request->validationData();
        $data['path'] = (new assetsService())->storeFile($request->file('file'));
        $data['user_id'] = auth()->user()->id;
        $task->attachments()->create($data);
        Cache::forget("task_$task->id");
        return 'added successfully';


    }
}
