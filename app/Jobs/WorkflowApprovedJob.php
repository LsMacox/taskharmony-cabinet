<?php

namespace App\Jobs;

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

class WorkflowApprovedJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Workflow $workflow, public WorkflowRepository $repository)
    {
    }

    public function handle(): void
    {
        $allAsGroupsIds = $this->repository->getAllGroupIdsFromApprovalSequence($this->workflow);
        $asUserIds = collect($this->workflow->approve_sequence)->pluck('user_id');

        $groupCountList = $this->countApprovedGroups($allAsGroupsIds);
        $userCount = $this->countApprovedUsers();

        if ($groupCountList >= $allAsGroupsIds->count() && $userCount >= $asUserIds->count()) {
            $this->workflow->state->transitionTo(Approved::class);
        }
    }

    private function countApprovedGroups($allAsGroupsIds)
    {
        $userWorkflowGroups = UserWorkflowApproval::with('group')
            ->where('workflow_id', $this->workflow->id)
            ->whereNotNull('group_id')
            ->where('is_approve', true)
            ->get();

        return $userWorkflowGroups->whereIn('group_id', $allAsGroupsIds)->count();
    }

    private function countApprovedUsers()
    {
        return UserWorkflowApproval::where('workflow_id', $this->workflow->id)
            ->whereNull('group_id')
            ->where('is_approve', true)
            ->count();
    }

    public function uniqueId(): string
    {
        return 'workflow-checker-id-' . $this->workflow->id;
    }
}
