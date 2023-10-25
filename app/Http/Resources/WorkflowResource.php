<?php

namespace App\Http\Resources;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkflowResource extends JsonResource
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
            'state' => $this->state,
            'approve_sequence' => $this->getApproveSequence(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    protected function getApproveSequence(): array
    {
        $approveSequence = [];

        $groupIds = collect($this->approve_sequence)->pluck('group_id')->filter()->unique()->toArray();
        $userIds = collect($this->approve_sequence)->pluck('user_id')->filter()->unique()->toArray();

        $groups = Group::whereIn('id', $groupIds)->get();
        $users = User::whereIn('id', $userIds)->get();

        foreach ($this->approve_sequence as $item) {
            if (isset($item['group_id'])) {
                $group = $groups->firstWhere('id', $item['group_id']);
                if ($group) {
                    $approveSequence[] = array_merge($group->toArray(), ['is_group' => true]);
                }
            }
            if (isset($item['user_id'])) {
                $user = $users->firstWhere('id', $item['user_id']);
                if ($user) {
                    $approveSequence[] = array_merge($user->toArray(), ['is_group' => false]);
                }
            }
        }

        return $approveSequence;
    }
}
