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
                'viewAny.Group',
                'view.Group',
            ],
            ['Employee'],
        ],
    ];
};
