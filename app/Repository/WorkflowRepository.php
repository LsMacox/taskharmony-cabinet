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
            return Workflow::where('id', null);
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

    public function getAllGroupIdsFromApprovalSequence($workflow, bool $is_group = false)
    {
        $asGroupIds = collect($workflow->approve_sequence)->pluck('group_id')
            ->filter()
            ->values()
            ->all();

        $asGroupsBuilder = Group::select('id', 'name')
            ->whereIn('id', $asGroupIds);

        if ($asGroupIds) {
            $asGroupsBuilder->orderByRaw('FIELD(id,' . implode(',', $asGroupIds) . ')');
        }

        $asGroups = $asGroupsBuilder->get();

        $allAsGroupsIds = collect()->merge($asGroupIds);

        if ($is_group) {
            $allAsGroupsIds = $asGroups->mapWithKeys(function ($group) {
                $childIds = $this->getFlatListOfSubgroups($group)->pluck('id');
                return [$group->id => $childIds];
            });
        } else {
            foreach ($asGroups as $group) {
                $groups = $this->getFlatListOfSubgroups($group)->pluck('id');
                $allAsGroupsIds = $allAsGroupsIds->merge($groups);
            }
        }

        return $allAsGroupsIds->unique()->filter();
    }

    public function getCounts(Workflow $workflow): array
    {
        $allGroupIds = $this->getAllGroupIdsFromApprovalSequence($workflow, true);

        $groups = $allGroupIds->mapWithKeys(function ($childIds, $key) {
            $groups = Group::whereIn('id', $childIds->merge($key))->get()->load('users');

            return [$key => $groups];
        });

        $totalCount = $groups->pluck('users')->flatten()->count();

        $userWorkflowApprovals = UserWorkflowApproval::where('workflow_id', $workflow->id)
            ->where('is_approve', true)
            ->get();

        $groupCountList = [];

        foreach ($groups as $groupId => $childGroups) {
            $mainGroup = Group::find($groupId);
            $groupUsers = $childGroups->pluck('users')->flatten();
            $approvedCount = $userWorkflowApprovals->whereIn('group_id', $childGroups->pluck('id'))
                ->whereNotNull('user_id')
                ->whereIn('user_id', $groupUsers->pluck('id'))
                ->count();

            $groupCountList[] = [
                'id' => $mainGroup->id,
                'name' => $mainGroup->name,
                'total_possible_approvals' => $groupUsers->count(),
                'approved_count' => $approvedCount
            ];
        }

        $groupApprovalsCount = collect($groupCountList)->sum('approved_count');
        $individualUserCount = $userWorkflowApprovals->whereNull('group_id')->count();

        return [
            'group_count_list' => $groupCountList,
            'individual_user_count' => $individualUserCount,
            'group_approvals_count' => $groupApprovalsCount,
            'total_count' => $totalCount
        ];
    }
}
