<?php

namespace App\Policies;

use Illuminate\Foundation\Auth\User as Authenticatable;

class UserWorkflowPolicy
{
    public function viewAny(Authenticatable $auth): bool
    {
        return $auth->can('viewAny.UserWorkflow');
    }

    public function view(Authenticatable $auth): bool
    {
        return $auth->can('view.UserWorkflow');
    }

    public function create(Authenticatable $auth): bool
    {
        return $auth->can('create.UserWorkflow');
    }

    public function update(Authenticatable $auth): bool
    {
        return $auth->can('update.UserWorkflow');
    }

    public function delete(Authenticatable $auth): bool
    {
        return $auth->can('delete.UserWorkflow');
    }
}
