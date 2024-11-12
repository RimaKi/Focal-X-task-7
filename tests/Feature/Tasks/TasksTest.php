<?php

namespace Tests\Feature\Tasks;

use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TasksTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $user_executor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role_id' => Role::query()->firstOrCreate(['name' => 'task_builder'])->id]);
        $this->user_executor = User::factory()->create(['role_id' => Role::query()->firstOrCreate(['name' => 'task_executor'])->id]);
        $this->actingAs($this->user);
    }

    /** @test */
    public function test_list_tasks()
    {
        $tasks = Task::factory(5)->create([
            'assigned_to' => $this->user->id
        ]);
        $response = $this->getJson(route('tasks.index'));
        $response->assertStatus(200)
            ->assertJson(['success' => true, 'message' => 'trans.method.GET.success'])
            ->assertJsonCount(5, 'data');
    }

    public function test_store_a_task_successfully()
    {
        $data = [
            'title' => 'New Task',
            'description' => 'Task description',
            'type' => 'bug',
            'priority' => 'high',
            'assigned_to' => User::factory()->create()->id,
            'dependencies' => []
        ];
        $response = $this->postJson(route('tasks.store'), $data);
        $response->assertStatus(200)->assertJsonFragment(['title' => 'New Task']);
    }


    public function test_show_task_details()
    {
        $task = Task::factory()->create();
        $response = $this->getJson(route('tasks.show', $task->id));
        $response->assertStatus(200)->assertJsonFragment(['id' => $task->id]);
    }


    //task not found
    public function test_fail_to_show_task_if_user_not_authorized()
    {
        $this->actingAs($this->user);
        $response = $this->getJson(route('tasks.show', 5));
        $response->assertStatus(404);
    }


    public function test_delete_task()
    {
        $task = Task::factory()->create();
        $response = $this->deleteJson(route('tasks.destroy', $task->id));
        $response->assertStatus(200)->assertSee('deleted successfully');
        $this->assertCount(0, Task::all());
    }


    public function test_progress_task_status()
    {
        $task = Task::factory()->create(['status' => 'open', 'assigned_to' => $this->user_executor]);
        $response = $this->actingAs($this->user_executor)->putJson("api/tasks/$task->id/progress");
        $response->assertStatus(200)->assertSee('updated status to in progress successfully');
    }


    public function test_complete_task()
    {
        $task = Task::factory()->create(['status' => 'inProgress', 'assigned_to' => $this->user_executor]);
        $response = $this->actingAs($this->user_executor)->putJson("api/tasks/$task->id/completed");
        $response->assertStatus(200)->assertSee('completed task successfully');
    }

    public function test_fail_reassign_task_if_user_not_exist()
    {
        $task = Task::factory()->create();
        $response = $this->putJson("api/tasks/{$task->id}/reassign/9}");
        $response->assertStatus(404);
    }

    public function test_successfully_reassign_task_for_user()
    {
        $user_manager = User::factory()->create(['role_id' => Role::query()->firstOrCreate(['name' => 'task_manager'])->id]);
        $task = Task::factory()->create();
        $response = $this->actingAs($user_manager)->putJson("api/tasks/{$task->id}/reassign/".$this->user->id);
        $response->assertStatus(200)->assertSee('reassign successfully');
    }


}
