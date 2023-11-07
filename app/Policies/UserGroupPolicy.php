<?php

namespace App\Policies;

use Illuminate\Foundation\Auth\User as Authenticatable;

class UserGroupPolicy
{
    public function create(Authenticatable $auth): bool
    {
            return auth()->user()->groups()?->withPivot('permissions')
                ->get()->pluck('pivot.permissions')->filter()->count() > 0;
    }
}
