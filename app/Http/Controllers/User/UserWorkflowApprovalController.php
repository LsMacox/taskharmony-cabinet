<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
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
        $asUserIds = collect($workflow->approve_sequence)->pluck('user_id');

        $allUserGroupIds = $this->repository->getAllGroupIdsForUser(auth()->user());
        $allAsGroupsIds = $this->repository->getAllGroupIdsFromApprovalSequence($workflow, true);
        $matchingGroupId = null;

        foreach ($allAsGroupsIds as $asGroupsId => $childIds) {
            if ($childIds->intersect($allUserGroupIds)->isNotEmpty()) {
                $matchingGroupId = $asGroupsId;
                break;
            }
        }

        $validator = $this->validator($workflow, $matchingGroupId, $isApprove);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if (!$matchingGroupId && !$asUserIds->contains(auth()->id())) {
            return response()->json(['message' => 'No matching group_id or user_id found'], 400);
        }

        $this->updateApproval($workflow, $matchingGroupId, $isApprove);

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

    private function validator(Workflow $workflow, ?int $userGroupId, bool $isApprove)
    {
        if ($isApprove) {
            $validationData = [
                'shouldBeApprove' => $this->shouldBeApprove($workflow, $userGroupId),
            ];
            $data = ['shouldBeApprove' => 'accepted'];
        } else {
            $validationData = [
                'shouldBeReject' => $this->shouldBeReject($workflow, $userGroupId),
            ];
            $data = ['shouldBeReject' => 'accepted'];
        }

        $validator = validator($validationData, $data, [
            'shouldBeApprove.accepted' => 'Approval is not allowed at this stage. The current group has not reached the required approvals or it is not their turn to approve yet.',
            'shouldBeReject.accepted' => 'Rejection is not possible at this stage.',
        ]);

        return $validator;
    }

    // TODO: Duplicate, biches-code
    private function shouldBeApprove(Workflow $workflow, ?int $userGroupId): bool
    {
        $approveSequence = collect($workflow->approve_sequence);
        $counts = $this->repository->getCounts($workflow);
        $groupId = $userGroupId;
        $indexBias = fn($index) => $index - 1;

        if (!$userGroupId) {
            $index = $approveSequence->where('user_id', auth()->id())->keys()->first();

            if ($index == 0) {
                return true;
            }

            $prevItem = $approveSequence[$index - 1];

            if (isset($prevItem['group_id'])) {
                $groupId = $prevItem['group_id'];
                $indexBias = fn($index) => $index;
            } else {
                return $counts['individual_user_count'] == $index;
            }
        }
        $index = $approveSequence->where('group_id', $userGroupId)->keys()->first();

        if ($index === $approveSequence->count() - 1) {
            return true;
        }

        $prevItem = $approveSequence[$index - 1];

        if (isset($prevItem['user_id'])) {
            return $counts['individual_user_count'] == $index;
        } else {
            $count = $this->getCount($workflow, $groupId, $indexBias);
            return !$count ||
                $count['total_possible_approvals'] !== 0 && $count['total_possible_approvals'] === $count['approved_count'];
        }
    }

    private function shouldBeReject(Workflow $workflow, ?int $userGroupId): bool
    {
        $approveSequence = collect($workflow->approve_sequence);
        $counts = $this->repository->getCounts($workflow);
        $indexBias = fn($index) => $index + 1;
        $groupId = $userGroupId;

        if (!$userGroupId) {
            $index = $approveSequence->where('user_id', auth()->id())->keys()->first();

            if ($index == $approveSequence->count() - 1) {
                return true;
            }

            $nextItem = $approveSequence[$index + 1];

            if (isset($nextItem['group_id'])) {
                $groupId = $nextItem['group_id'];
                $indexBias = fn($index) => $index;
            } else {
                return $counts['individual_user_count'] == $index + 1;
            }
        }

        $index = $approveSequence->where('group_id', $userGroupId)->keys()->first();

        if ($index == $approveSequence->count() - 1) {
            return true;
        }

        $nextItem = $approveSequence[$index + 1];

        if (isset($nextItem['user_id'])) {
            return $counts['individual_user_count'] == $index + 1;
        } else {
            $count = $this->getCount($workflow, $groupId, $indexBias);
            return $count['approved_count'] === 0;
        }
    }

    private function getCount(Workflow $workflow, ?int $userGroupId, \Closure $indexBias)
    {
        $counts = $this->repository->getCounts($workflow);
        $groupCountList = collect($counts['group_count_list']);
        $index = $groupCountList->search(function ($item) use ($userGroupId) {
            return $item['id'] == $userGroupId;
        });

        return $groupCountList->values()->get($indexBias($index));
    }
}
