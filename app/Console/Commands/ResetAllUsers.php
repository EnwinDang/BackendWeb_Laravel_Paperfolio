<?php

namespace App\Console\Commands;

use App\Models\Trade;
use Illuminate\Console\Command;

class ResetAllUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset all users by deleting all trades (users will start fresh with $1,000)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->confirm('Are you sure you want to delete ALL trades? This will reset all users back to $1,000. This action cannot be undone.')) {
            $this->info('Reset cancelled.');
            return 0;
        }

        $tradeCount = Trade::count();
        
        Trade::truncate();
        
        $this->info("Successfully deleted {$tradeCount} trades.");
        $this->info('All users have been reset and now have $1,000 available cash.');
        
        return 0;
    }
}
