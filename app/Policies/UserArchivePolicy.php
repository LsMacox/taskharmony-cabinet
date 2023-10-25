<?php

namespace App\Policies;

use Illuminate\Foundation\Auth\User as Authenticatable;

class UserArchivePolicy
{
    public function view(Authenticatable $auth): bool
    {
        return $auth->can('view.UserArchive');
    }
}
