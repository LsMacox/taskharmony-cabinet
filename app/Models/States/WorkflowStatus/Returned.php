<?php

namespace App\Models\States\WorkflowStatus;

class Returned extends WorkflowStatusState
{
    public static $name = 'returned';

    public function status(): string
    {
        return 'returned';
    }
}
