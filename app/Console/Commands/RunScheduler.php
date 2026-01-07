<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RunScheduler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scheduler:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the scheduler continuously (runs scheduled tasks when due)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Scheduler started. Press Ctrl+C to stop.');
        $this->info('Price updates will run every 5 minutes automatically.');
        $this->newLine();

        while (true) {
            // Run the scheduler (it will only execute tasks that are due)
            Artisan::call('schedule:run');
            
            $output = Artisan::output();
            if (!empty(trim($output))) {
                $this->line($output);
            }
            
            // Sleep for 60 seconds before checking again
            sleep(60);
        }
    }
}
