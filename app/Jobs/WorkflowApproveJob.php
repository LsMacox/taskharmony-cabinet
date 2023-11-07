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

class WorkflowApproveJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public WorkflowRepository $repository;

    public function __construct(public Workflow $workflow)
    {
        $this->repository = app(WorkflowRepository::class);
    }

    public function handle(): void
    {
        $allAsGroupsIds = $this->repository->getAllGroupIdsFromApprovalSequence($this->workflow);
        $allAsGroupUserIds = collect();
        $allAsGroups = Group::whereIn('id', $allAsGroupsIds)->get()->load('users');

        foreach ($allAsGroupsIds as $groupsId) {
            $group = $allAsGroups->firstWhere('id', $groupsId);
            $allAsGroupUserIds->push($this->repository->getAllUserIdsForGroup($group));
        }

        $allAsGroupUserIds = $allAsGroupUserIds->unique();

        $asUserIds = collect($this->workflow->approve_sequence)->pluck('user_id')->filter();

        $groupCountList = $this->countApprovedGroups($allAsGroupsIds);
        $userCount = $this->countApprovedUsers();

        if ($groupCountList >= $allAsGroupsIds->count() - 1 && $userCount >= $asUserIds->count()) {
            $this->workflow->state->transitionTo(Approved::class);

            foreach ($allAsGroupUserIds->merge($asUserIds)->flatten()->unique() as $userId) {
                Notification::create([
                    'title' => 'Workflow has been approved!',
                    'description' => 'Workflow "' . $this->workflow->name . ' (' . $this->workflow->id .')" has been approved!',
                    'user_id' => $userId,
                ]);
            }
        }
    }

    private function countApprovedGroups($allAsGroupsIds): int
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
