<?php

namespace App\Models\States\WorkflowStatus;

use App\Models\States\WorkflowStatusState;

class Rejected extends WorkflowStatusState
{
    public function status(): string
    {
        return 'rejected';
    }
}
