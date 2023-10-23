<?php

namespace App\Policies;

use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Auth\Access\Response;

class UserPolicy
{
    public function archive(Authenticatable $auth): bool
    {
        return $auth->can('archive.User');
    }
}
