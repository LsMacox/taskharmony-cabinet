<?php

namespace App\Console\Commands;

use App\Jobs\WorkflowApprovedJob;
use App\Models\States\WorkflowStatus\InProgress;
use App\Models\Workflow;
use Illuminate\Console\Command;

class WorkflowChecker extends Command
{
    protected $signature = 'workflow:checker';

    protected $description = 'Сhecks the frames and finishes the workflow';

    public function handle()
    {
        foreach (Workflow::approved()->whereState('state', InProgress::class)->cursor() as $workflow) {
            WorkflowApprovedJob::dispatch($workflow);
        }
    }
}
