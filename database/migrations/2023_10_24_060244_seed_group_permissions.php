<?php

use App\Database\Migration;

return new class extends Migration
{
    const NEW_PERMISSIONS = [
        [
            [
                'viewAny.Group',
                'view.Group',
                'create.Group',
                'update.Group',
                'delete.Group',
            ],
            ['Super Admin'],
        ],
        [
            [
                'viewAny.UserGroup',
                'view.UserGroup',
                'create.UserGroup',
                'update.UserGroup',
                'delete.UserGroup',
            ],
            ['Employee'],
        ],
    ];
};
