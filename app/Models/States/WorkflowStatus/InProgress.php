<?php

namespace App\Models\States\WorkflowStatus;

use App\Models\States\WorkflowStatusState;

class InProgress extends WorkflowStatusState
{
    public function status(): string
    {
        return 'in_progress';
    }
}
