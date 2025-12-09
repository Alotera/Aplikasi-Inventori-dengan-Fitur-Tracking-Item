<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WorkInstruction;

class UpdateWorkInstructionStatus extends Command
{
    protected $signature = 'wi:update-status';
    protected $description = 'Update status for all work instructions based on deadline and completion';

    public function handle()
    {
        $this->info('Updating work instruction statuses...');
        
        $workInstructions = WorkInstruction::all();
        $updated = 0;
        
        foreach ($workInstructions as $wi) {
            $oldStatus = $wi->status;
            $wi->updateStatus();
            
            if ($oldStatus !== $wi->status) {
                $updated++;
                $this->line("Updated WI {$wi->wi_number}: {$oldStatus} → {$wi->status}");
            }
        }
        
        $this->info("Updated {$updated} work instructions out of {$workInstructions->count()} total.");
        
        // Show overdue count
        $overdueCount = WorkInstruction::where('status', 'overdue')->count();
        $this->info("Total overdue work instructions: {$overdueCount}");
        
        return Command::SUCCESS;
    }
}
