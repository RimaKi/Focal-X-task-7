<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskDependency;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaskService
{
    /**
     * التحقق أن جميع المهام التي تعتمد عليها المهمة مكتملة
     * @param $dependenciesData
     * @return bool
     */
    protected function checkDependenciesCompleted($dependenciesData)
    {
        foreach ($dependenciesData as $dependency) {
            if (Task::find($dependency)->status != 'completed') {
                return false;
            }
        }
        return true;
    }

    /**
     * @param array $data
     * @return mixed
     * @throws \Exception
     */
    public function store(array $data)
    {
        try {
            $data['status'] = 'open';
            if (isset($data['dependencies'])) {
                $data['status'] = $this->checkDependenciesCompleted($data['dependencies']) ? 'open' : 'blocked';
            }
            DB::beginTransaction();
            $task = Task::create($data);
            if (isset($data['dependencies'])) {
                foreach ($data['dependencies'] as $dep) {
                    TaskDependency::create([
                        'task_id' => $task->id,
                        'depends_on_task_id' => $dep
                    ]);
                }
            }
            Cache::flush();
            DB::commit();
            return $task->load('dependencies');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating Task: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * View task list with ability filters
     * @param array $data
     * @return mixed
     */
    public function tasksList(array $data)
    {
        $key = 'tasks_' . md5(json_encode($data));
        $data = Cache::remember($key, 3600, function () use ($data) {
            $tasks = Task::query();
            foreach ($data as $i => $item) {
                if ($i == 'depends_on') {
                    if ($item == null) {
                        $tasks = $tasks->whereDoesntHave('dependencies');
                    } else {
                        $tasks = $tasks->whereHas('dependencies',function ($q) use($item){
                            return $q->where('depends_on_task_id',$item);
                        });
                    }
                } else {
                    $tasks = $tasks->where($i, $item);
                }
            }
            return $tasks->latest()->get();
        });
        return $data;
    }
}
