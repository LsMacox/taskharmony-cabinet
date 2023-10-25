<?php

namespace App\Policies;

use Illuminate\Foundation\Auth\User as Authenticatable;

class UserWorkflowApprovalPolicy
{
    public function approve(Authenticatable $auth): bool
    {
        return $auth->can('approve.UserWorkflowApproval');
    }

    public function reject(Authenticatable $auth): bool
    {
        return $auth->can('reject.UserWorkflowApproval');
    }
}
