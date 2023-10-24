<?php

use App\Database\Migration;

return new class extends Migration
{
    const NEW_PERMISSIONS = [
        [
            [
                'viewAny.WorkflowRequest',
                'view.WorkflowRequest',
                'create.WorkflowRequest',
                'update.WorkflowRequest',
                'delete.WorkflowRequest',
            ],
            ['Super Admin'],
        ],
        [
            [
                'viewAny.UserWorkflowRequest',
                'view.UserWorkflowRequest',
                'create.UserWorkflowRequest',
                'update.UserWorkflowRequest',
                'delete.UserWorkflowRequest',
            ],
            ['Employee'],
        ],
    ];
};
