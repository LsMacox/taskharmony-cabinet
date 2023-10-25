<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\UserWorkflowApproval;
use App\Models\Workflow;
use App\Repository\WorkflowRepository;
use Illuminate\Http\JsonResponse;

class UserWorkflowApprovalController extends Controller
{
    public function __construct(
        public WorkflowRepository $repository
    )
    {
    }

    public function approve(int $workflow): JsonResponse
    {
        return $this->handleApproval($workflow, true);
    }

    public function reject(int $workflow): JsonResponse
    {
        return $this->handleApproval($workflow, false);
    }

    public function handleApproval(int $workflow, bool $isApprove): JsonResponse
    {
        $this->authorize('create', [UserWorkflowApproval::class]);

        $workflow = $this->repository->getUserWorkflowBuilder(auth()->user())->findOrFail($workflow);
        $approvalSequenceUserIds = collect($workflow->approve_sequence)->pluck('user_id');

        $allUserGroupIds = $this->repository->getAllGroupIdsForUser(auth()->user());
        $allAsGroupsIds = $this->repository->getAllGroupIdsFromApprovalSequence($workflow);

        $matchingGroupIds = $allAsGroupsIds->intersect($allUserGroupIds);

        if ($matchingGroupIds->isEmpty() && !$approvalSequenceUserIds->contains(auth()->id())) {
            return response()->json(['message' => 'No matching group_id or user_id found'], 400);
        }

        $this->updateApproval($workflow, $matchingGroupIds->first(), $isApprove);

        return response()->json(['message' => 'Successfully!']);
    }

    private function updateApproval(Workflow $workflow, ?int $groupId, bool $isApprove): void
    {
        UserWorkflowApproval::updateOrCreate(
            [
                'workflow_id' => $workflow->id,
                'user_id' => auth()->id(),
                'group_id' => $groupId,
            ],
            ['is_approve' => $isApprove]
        );
    }
}
