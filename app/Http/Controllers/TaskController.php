<?php

namespace App\Http\Controllers;

use App\Http\Requests\Task\StoreRequest;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TaskController extends Controller
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Display a listing of the resource.
     */
    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $data = Cache::remember('tasks', 3600, function () {
            return Task::latest();
        });
        if ($request->has('status')) {
            $data = $data->where('status', $request->status);
        }
        if ($request->has('type')) {
            $data = $data->where('type', $request->type);
        }
        if ($request->has('priority')) {
            $data = $data->where('priority', $request->priority);
        }
        return $data->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * @param StoreRequest $request
     * @return mixed
     * @throws \Exception
     */
    public function store(StoreRequest $request)
    {
        $data = $request->validationData();
        Cache::forget('tasks');
        return $this->taskService->store($data);
    }

    /**
     * Display the specified resource.
     */
    /**
     * @param Task $task
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Task $task)
    {
        $this->authorize('view', $task);
        return Cache::remember("task_$task->id", 3600, function () use ($task) {
            return $task->load(['assigned_to', 'taskStatusUpdates', 'dependentTasks', 'dependencies', 'comments', 'attachments']);
        });
    }


    /**
     * Remove the specified resource from storage.
     */
    /**
     * @param Task $task
     * @return string
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        if (!$task->delete()) {
            throw new \Exception('wrong in delete');
        }
        return 'deleted successfully';
    }

    /**
     * @param Task $task
     * @return string
     */
    public function progressTask(Task $task)
    {
        $task->update(['status' => 'inProgress']);
        Cache::forget("task_$task->id");
        return 'updated status to in progress successfully';
    }

    /**
     * @param Task $task
     * @return string
     */
    public function completedTask(Task $task)
    {
        $task->update([
            'due_date' => Carbon::now(),
            'status' => 'completed'
        ]);
        Cache::forget("task_$task->id");
        return 'completed task successfully';
    }

    /**
     * @param Task $task
     * @param User $user
     * @return string
     * @throws \Exception
     */
    public function reassign(Task $task, User $user)
    {
        if (!$task->update(['assigned_to' => $user->id])) {
            throw new \Exception('reassign error');
        }
        Cache::forget("task_$task->id");
        return 'reassign successfully';
    }
}
