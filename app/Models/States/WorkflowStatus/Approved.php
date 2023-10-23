<?php

namespace App\Models\States\WorkflowStatus;

use App\Models\States\WorkflowStatusState;

class Approved extends WorkflowStatusState
{
    public function status(): string
    {
        return 'approved';
    }
}
