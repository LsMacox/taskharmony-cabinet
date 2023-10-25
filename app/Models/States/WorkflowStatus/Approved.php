<?php

namespace App\Models\States\WorkflowStatus;

class Approved extends WorkflowStatusState
{
    public static $name = 'approved';

    public function status(): string
    {
        return 'approved';
    }
}
