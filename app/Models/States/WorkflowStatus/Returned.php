<?php

namespace App\Models\States\WorkflowStatus;

use App\Models\States\WorkflowStatusState;

class Returned extends WorkflowStatusState
{
    public function status(): string
    {
        return 'returned';
    }
}
