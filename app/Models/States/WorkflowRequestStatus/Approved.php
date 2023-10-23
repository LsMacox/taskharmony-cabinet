<?php

namespace App\Models\States\WorkflowRequestStatus;

use App\Models\States\WorkflowStatusState;

class Approved extends WorkflowStatusState
{
    public function status(): string
    {
        return 'approved';
    }
}
