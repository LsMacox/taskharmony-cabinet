<?php

use App\Database\Migration;

return new class extends Migration
{
    const NEW_PERMISSIONS = [
        [
            [
                'viewAny.Workflow',
                'view.Workflow',
                'create.Workflow',
                'update.Workflow',
                'delete.Workflow',
            ],
            ['Super Admin'],
        ],
        [
            [
                'viewAny.UserWorkflow',
                'view.UserWorkflow',
                'create.UserWorkflow',
                'update.UserWorkflow',
                'delete.UserWorkflow',
            ],
            ['Super Admin', 'Employee'],
        ],
    ];
};
