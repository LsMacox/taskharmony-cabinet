<?php

namespace App\Jobs;

use App\Models\Group;
use App\Models\Notification;
use App\Models\States\WorkflowStatus\Approved;
use App\Models\UserWorkflowApproval;
use App\Models\Workflow;
use App\Repository\WorkflowRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ApprovalCleanJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public WorkflowRepository $repository;

    public function __construct(public Workflow $workflow)
    {
        $this->repository = app(WorkflowRepository::class);
    }

    public function handle(): void
    {
        UserWorkflowApproval::where('workflow_id', $this->workflow->id)->delete();
    }
}
