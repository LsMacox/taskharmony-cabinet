<?php

namespace App\Policies;

use Illuminate\Foundation\Auth\User as Authenticatable;

class UserWorkflowApprovalPolicy
{
    public function create(Authenticatable $auth): bool
    {
        return $auth->can('create.UserWorkflowApproval');
    }
}
