<?php

namespace App\Models\States\WorkflowStatus;

class Rejected extends WorkflowStatusState
{
    public static $name = 'rejected';

    public function status(): string
    {
        return 'rejected';
    }
}
