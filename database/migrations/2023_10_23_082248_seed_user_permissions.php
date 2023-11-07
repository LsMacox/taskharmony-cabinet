<?php

use App\Database\Migration;

return new class extends Migration
{
    const NEW_PERMISSIONS = [
        [
            [
                'viewAny.User',
                'view.User',
                'create.User',
                'update.User',
                'delete.User',
            ],
            ['Super Admin'],
        ],
    ];
};
