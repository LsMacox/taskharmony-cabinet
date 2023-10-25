<?php

namespace App\Models\States\WorkflowStatus;

class InProgress extends WorkflowStatusState
{
    public static $name = 'in_progress';

    public function status(): string
    {
        return 'in_progress';
    }
}
