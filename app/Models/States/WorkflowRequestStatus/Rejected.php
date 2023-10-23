<?php

namespace App\Models\States\WorkflowRequestStatus;

use App\Models\States\WorkflowStatusState;

class Rejected extends WorkflowStatusState
{
    public function status(): string
    {
        return 'rejected';
    }
}
