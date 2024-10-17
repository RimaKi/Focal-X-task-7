<?php

namespace App\Console\Commands;

use App\Jobs\SendEmailJob;
use Illuminate\Console\Command;

class SendDailyTasksReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:daily-tasks';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a daily email report of incomplete tasks.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        SendEmailJob::dispatch();
        $this->info('Daily tasks report sent successfully!');
    }
}
