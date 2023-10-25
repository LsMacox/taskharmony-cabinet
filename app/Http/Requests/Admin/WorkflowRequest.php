<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\User\WorkflowRequest as UserWorkflowRequest;
use App\Models\States\WorkflowStatus\WorkflowStatusState;
use App\Models\Workflow;
use Illuminate\Validation\Rule;
use Spatie\ModelStates\Validation\ValidStateRule;

class WorkflowRequest extends UserWorkflowRequest
{
    public function authorize(): bool
    {
        return auth()->guard()->check();
    }

    public function rules(): array
    {
        $rules = array_merge(parent::rules(), [
            'state' => ValidStateRule::make(WorkflowStatusState::class)->nullable(),
            'status' => ['string', Rule::in(array_keys(Workflow::MAP_STRING_STATUSES))],
            'group_id' => ['integer', 'exists:groups,id'],
        ]);

        if ($this->method() == 'POST') {
            $rules['group_id'][] = 'required';
        }

        return $rules;
    }
}
