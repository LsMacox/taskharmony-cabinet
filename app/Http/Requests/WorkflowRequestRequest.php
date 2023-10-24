<?php

namespace App\Http\Requests;

use App\Models\States\WorkflowRequestStatusState;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\ModelStates\Validation\ValidStateRule;

class WorkflowRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->guard()->check();
    }

    public function rules(): array
    {
        $rules = [
            'workflow_id' => ['exists:workflows,id'],
            'status' => ValidStateRule::make(WorkflowRequestStatusState::class)->nullable(),
        ];

        if ($this->method() === 'POST') {
            $rules['workflow_id'][] = 'required';
        }

        return $rules;
    }
}
