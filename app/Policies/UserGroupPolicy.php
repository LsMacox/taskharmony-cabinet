<?php

namespace App\Policies;

use Illuminate\Foundation\Auth\User as Authenticatable;

class UserGroupPolicy
{
    public function viewAny(Authenticatable $auth): bool
    {
        return $auth->can('viewAny.UserGroup');
    }

    public function view(Authenticatable $auth): bool
    {
        return $auth->can('view.UserGroup');
    }

    public function create(Authenticatable $auth): bool
    {
        return $auth->can('create.UserGroup');
    }

    public function update(Authenticatable $auth): bool
    {
        return $auth->can('update.UserGroup');
    }

    public function delete(Authenticatable $auth): bool
    {
        return $auth->can('delete.UserGroup');
    }
}
