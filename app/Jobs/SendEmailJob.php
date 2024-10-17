<?php

namespace App\Jobs;

use App\Mail\SendEmail;
use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $tasks = Task::query()->whereNot('status', 'complete')->with('taskStatusUpdates')->get();
        $users = User::query()->whereRelation('role', function ($q) {
            return $q->where('name', 'task_builder');
        })->pluck('email');
        foreach ($users as $user) {
            Mail::to($user)->send(new SendEmail($tasks));
        }
    }
}
