<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class GroupUser extends Pivot
{
    protected $table = 'groups_users';

    protected $fillable = [
        'permissions',
    ];

    const PERMISSION_CREATE = 'create';

    const PERMISSIONS = [
        self::PERMISSION_CREATE,
    ];

    protected $attributes = [
        'permissions' => '{}',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];
}
