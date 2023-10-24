<?php

namespace App\Policies;

use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Auth\Access\Response;

class UserPolicy
{
    public function viewAny(Authenticatable $auth): bool
    {
        return $auth->can('viewAny.User');
    }

    public function view(Authenticatable $auth): bool
    {
        return $auth->can('view.User');
    }

    public function create(Authenticatable $auth): bool
    {
        return $auth->can('create.User');
    }

    public function update(Authenticatable $auth): bool
    {
        return $auth->can('update.User');
    }

    public function delete(Authenticatable $auth): bool
    {
        return $auth->can('delete.User');
    }
}
