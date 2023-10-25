<?php

namespace App\Repository;

use App\Models\Group;
use App\Models\User;
use App\Models\UserWorkflowApproval;
use App\Models\Workflow;

class WorkflowRepository
{
    // TODO: Group feature
    public function getFlatListOfSubgroups(Group $group)
    {
        $flatList = collect([]);

        $traverse = function ($group, $list) use (&$traverse) {
            $list->push($group);

            foreach ($group->children as $child) {
                $traverse($child, $list);
            }
        };

        $traverse($group, $flatList);

        return $flatList;
    }

    public function getUserWorkflowBuilder(User $user)
    {
        $groupUsersMap = [];

        Group::with('users')->get()->each(function ($group) use (&$groupUsersMap) {
            $groupUsersMap[$group->id] = $this->getAllUserIdsForGroup($group);
        });

        $workflowIds = [];

        // TODO: super-duper non-optimize
        Workflow::chunk(500, function ($workflows) use ($user, &$workflowIds, $groupUsersMap) {
            foreach ($workflows as $workflow) {
                foreach ($workflow->approve_sequence as $item) {
                    if (isset($item['group_id'])) {
                        $groupUsers = $groupUsersMap[$item['group_id']] ?? [];

                        if (in_array($user->id, $groupUsers)) {
                            $workflowIds[] = $workflow->id;
                            break;
                        }
                    }

                    if (isset($item['user_id']) && $item['user_id'] === $user->id) {
                        $workflowIds[] = $workflow->id;
                        break;
                    }
                }
            }
        });

        if (!empty($workflowIds)) {
             return Workflow::whereIn('id', $workflowIds);
        } else {
            abort(404);
        }
    }

    public function getAllUserIdsForGroup(Group $group): array
    {
        $userIds = $group->users->pluck('id')->toArray();

        foreach ($group->children as $childGroup) {
            $userIds = array_merge($userIds, $this->getAllUserIdsForGroup($childGroup));
        }

        return array_unique($userIds);
    }

    public function getAllGroupIdsForUser($user)
    {
        $allUserGroupIds = collect();

        foreach ($user->groups as $group) {
            $groups = $this->getFlatListOfSubgroups($group)->pluck('id');
            $allUserGroupIds = $allUserGroupIds->merge($groups);
        }

        return $allUserGroupIds->unique();
    }

    public function getAllGroupIdsFromApprovalSequence($workflow)
    {
        $asGroupIds = collect($workflow->approve_sequence)->pluck('group_id');
        $asGroups = Group::whereIn('id', $asGroupIds)->get();

        $allAsGroupsIds = collect()->merge($asGroupIds);

        foreach ($asGroups as $group) {
            $groups = $this->getFlatListOfSubgroups($group)->pluck('id');
            $allAsGroupsIds = $allAsGroupsIds->merge($groups);
        }

        return $allAsGroupsIds->unique();
    }

    public function getCounts(Workflow $workflow): array
    {
        $allAsGroupsIds = $this->getAllGroupIdsFromApprovalSequence($workflow);

        $userWorkflowGroups = UserWorkflowApproval::with('group')
            ->where('workflow_id', $workflow->id)
            ->whereNotNull('group_id')
            ->where('is_approve', true)
            ->get();

        $groupCountList = [];
        $userCount = UserWorkflowApproval::where('workflow_id', $workflow->id)
            ->whereNull('group_id')
            ->where('is_approve', true)
            ->count();

        foreach ($allAsGroupsIds as $groupsId) {
            $group = $userWorkflowGroups->where('group_id', $groupsId)->first()?->group;

            if ($group) {
                $groupCountList[] = [
                    'id' => $groupsId,
                    'name' => $group->name,
                    'count' => $userWorkflowGroups->where('group_id', $groupsId)->count(),
                ];
            }
        }

        $groupCount = array_reduce($groupCountList, function ($carry, $item) {
            return $carry + $item['count'];
        }, 0);

        return compact('groupCountList', 'userCount', 'groupCount');
    }
}
