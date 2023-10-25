<?php

namespace App\Policies;

use Illuminate\Foundation\Auth\User as Authenticatable;

class GroupPolicy
{
    public function viewAny(Authenticatable $auth): bool
    {
        return $auth->can('viewAny.Group');
    }

    public function view(Authenticatable $auth): bool
    {
        return $auth->can('view.Group');
    }

    public function create(Authenticatable $auth): bool
    {
        return $auth->can('create.Group');
    }

    public function update(Authenticatable $auth): bool
    {
        return $auth->can('update.Group');
    }

    public function delete(Authenticatable $auth): bool
    {
        return $auth->can('delete.Group');
    }
}
