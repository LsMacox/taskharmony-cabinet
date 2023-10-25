<?php

use App\Database\Migration;

return new class extends Migration
{
    const NEW_PERMISSIONS = [
        [
            [
                'view.UserArchive',
            ],
            ['Super Admin', 'Employee'],
        ],
    ];
};
