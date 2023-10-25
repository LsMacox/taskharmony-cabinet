<?php

namespace App\Policies;

use Illuminate\Foundation\Auth\User as Authenticatable;

class WorkflowPolicy
{
    public function viewAny(Authenticatable $auth): bool
    {
        return $auth->can('viewAny.Workflow');
    }

    public function view(Authenticatable $auth): bool
    {
        return $auth->can('view.Workflow');
    }

    public function create(Authenticatable $auth): bool
    {
        return $auth->can('create.Workflow');
    }

    public function update(Authenticatable $auth): bool
    {
        return $auth->can('update.Workflow');
    }

    public function delete(Authenticatable $auth): bool
    {
        return $auth->can('delete.Workflow');
    }
}
