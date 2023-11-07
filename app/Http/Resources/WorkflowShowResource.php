<?php

namespace App\Http\Resources;

use App\Models\Group;
use App\Models\User;
use App\Models\UserWorkflowApproval;
use App\Repository\WorkflowRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkflowShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'group_id' => $this->group_id,
            'group_name' => $this->group->name,
            'state' => $this->state,
            'approve_sequence' => $this->getApproveSequence(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    protected function getApproveSequence(): array
    {
        $repository = app(WorkflowRepository::class);
        $approveSequence = [];

        $groupIds = $this->getUniqueIds('group_id');
        $userIds = $this->getUniqueIds('user_id');

        $groups = Group::findMany($groupIds);
        $users = User::findMany($userIds);

        $allAsGroupsIds = $repository->getAllGroupIdsFromApprovalSequence($this->resource, true);
        $userWorkflowApprovals = $this->getUserWorkflowApprovals();

        foreach ($this->approve_sequence as $item) {
            $approveSequence[] =
                $this->getApprovalData($item, $groups, $users, $userWorkflowApprovals, $allAsGroupsIds);
        }

        return $approveSequence;
    }

    private function getUniqueIds(string $key): array
    {
        return collect($this->approve_sequence)
            ->pluck($key)
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }

    private function getUserWorkflowApprovals()
    {
        return UserWorkflowApproval::
            where('workflow_id', $this->id)
            ->where('is_approve', true)
            ->get();
    }

    private function getApprovalData($item, $groups, $users, $userWorkflowApprovals, $allAsGroupsIds)
    {
        if (isset($item['group_id'])) {
            return $this->getGroupApprovalData($item['group_id'], $groups, $userWorkflowApprovals, $allAsGroupsIds);
        }

        if (isset($item['user_id'])) {
            return $this->getUserApprovalData($item['user_id'], $users, $userWorkflowApprovals);
        }

        return null;
    }

    private function getGroupApprovalData(int $groupId, $groups, $userWorkflowApprovals, $allAsGroupsIds)
    {
        $group = $groups->firstWhere('id', $groupId);
        if (!$group) {
            return null;
        }

        $userWorkflowApprovalCount = $userWorkflowApprovals->where('group_id', $groupId)->count();

        $userForAssertCount = Group::whereIn('id', $allAsGroupsIds[$groupId]->unique())
            ->with('users')
            ->get()
            ->pluck('users.*.id')
            ->collapse()
            ->count();

        $data = array_merge($group->toArray(), [
            'is_group' => true,
            'is_approve' => $userWorkflowApprovalCount >= $userForAssertCount && $userForAssertCount > 0,
        ]);

        return $data;
    }

    private function getUserApprovalData(int $userId, $users, $userApprovals)
    {
        $user = $users->firstWhere('id', $userId);
        if ($user) {
            $data = array_merge($user->toArray(), ['is_group' => false]);
            $isApproved = $userApprovals->whereNull('group_id')->where('user_id', $userId)->isNotEmpty();

            $data['is_approve'] = $isApproved;

            return $data;
        }
        return null;
    }
}
