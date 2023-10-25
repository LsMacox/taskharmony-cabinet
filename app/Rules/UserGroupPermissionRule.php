<?php

namespace App\Rules;

use App\Models\GroupUser;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UserGroupPermissionRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user_group = auth()->user()
            ->groups()
            ->where('groups.id', $value)
            ->withPivot('permissions')
            ->first();

        if ($user_group &&
            $user_group->pivot['permissions'] &&
            in_array(GroupUser::PERMISSION_CREATE, $user_group->pivot['permissions'])) {
            return;
        }

        $fail('You do not have permission to create in this group.');
    }
}
