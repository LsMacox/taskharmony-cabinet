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
    const PERMISSION_VIEW = 'view';

    const PERMISSIONS = [
        self::PERMISSION_CREATE,
        self::PERMISSION_VIEW,
    ];

    protected $attributes = [
        'permissions' => '{}',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];
}
