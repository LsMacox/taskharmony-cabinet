<?php

namespace App\Policies;

use Illuminate\Foundation\Auth\User as Authenticatable;

class UserWorkflowRequestPolicy
{
    public function viewAny(Authenticatable $auth): bool
    {
        return $auth->can('viewAny.UserWorkflowRequest');
    }

    public function view(Authenticatable $auth): bool
    {
        return $auth->can('view.UserWorkflowRequest');
    }

    public function create(Authenticatable $auth): bool
    {
        return $auth->can('create.UserWorkflowRequest');
    }

    public function update(Authenticatable $auth): bool
    {
        return $auth->can('update.UserWorkflowRequest');
    }

    public function delete(Authenticatable $auth): bool
    {
        return $auth->can('delete.UserWorkflowRequest');
    }
}
