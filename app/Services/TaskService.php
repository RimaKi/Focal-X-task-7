<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskDependency;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaskService
{
    /**
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
            DB::commit();
            return $task->load('dependencies');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating Task: ' . $e->getMessage());
            throw new \Exception('there is something wrong in server');
        }
    }


}
