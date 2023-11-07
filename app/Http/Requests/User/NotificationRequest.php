<?php

namespace App\Http\Requests\User;

use App\Models\States\WorkflowRequestStatus\WorkflowRequestStatusState;
use Illuminate\Foundation\Http\FormRequest;

class NotificationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'ids' => ['required', 'exists:notifications,id'],
        ];
    }
}
