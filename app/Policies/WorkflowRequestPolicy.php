<?php

namespace App\Policies;

use Illuminate\Foundation\Auth\User as Authenticatable;

class WorkflowRequestPolicy
{
    public function viewAny(Authenticatable $auth): bool
    {
        return $auth->can('viewAny.WorkflowRequest');
    }

    public function view(Authenticatable $auth): bool
    {
        return $auth->can('view.WorkflowRequest');
    }

    public function create(Authenticatable $auth): bool
    {
        return $auth->can('create.WorkflowRequest');
    }

    public function update(Authenticatable $auth): bool
    {
        return $auth->can('update.WorkflowRequest');
    }

    public function delete(Authenticatable $auth): bool
    {
        return $auth->can('delete.WorkflowRequest');
    }
}
