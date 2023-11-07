<?php

namespace App\Http\Requests\User;

use App\Models\Group;
use App\Models\States\WorkflowRequestStatus\WorkflowRequestStatusState;
use App\Repository\WorkflowRepository;
use App\Rules\UserGroupPermissionRule;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\ModelStates\Validation\ValidStateRule;

class WorkflowRequest extends FormRequest
{
    public function __construct(
        public WorkflowRepository $repository
    )
    {
    }

    public function rules(): array
    {
        $rules = [
            'name' => [
                'string',
                Rule::unique('workflows')->where(function ($query) {
                    return $query->where('group_id', $this->group_id);
                }),
            ],
            'approve_sequence' => ['nullable', 'array', $this->validationApproveSequence()],
            'approve_sequence.*.group_id' => ['integer', 'exists:groups,id'],
            'approve_sequence.*.user_id' => ['integer', 'exists:users,id'],
        ];

        if ($this->method() === 'POST') {
            $rules['name'][] = 'required';
            $rules['group_id'] = [
                'required',
                'integer',
                Rule::exists('groups_users', 'group_id')->where(function ($query) {
                    $query->where('user_id', $this->user()->id);
                }),
                new UserGroupPermissionRule(),
            ];
        }

        return $rules;
    }

    public function validationApproveSequence(): Closure
    {
        return function ($attribute, $value, $fail) {
            $group = Group::with(['children.users'])->find($this->group_id);

            if ($group) {
                $flatChildren = $this->repository->getFlatListOfSubgroups($group);

                $flatChildrenUserIds = $flatChildren->pluck('users.*.id')->flatten()->unique();

                foreach ($value as $item) {
                    if (isset($item['group_id'])) {
                        $check = $flatChildren->pluck('id')->contains($item['group_id']);
                        if (!$check) {
                            $fail('The provided group_id is invalid. The \'group_id\' must be a child of the main \'group_id\'.');
                        }
                    } elseif (isset($item['user_id'])) {
                        $check = !$flatChildrenUserIds->contains($item['user_id']);
                        if (!$check) {
                            $fail('The user being added should not belong to the current group or any of its child groups.');
                        }
                    }
                }
            }
        };
    }
}
