<?php

namespace App\Models\States;

use App\Models\States\WorkflowRequestStatus\InProgress;
use App\Models\States\WorkflowRequestStatus\Rejected;
use App\Models\States\WorkflowRequestStatus\Approved;
use Spatie\ModelStates\Exceptions\InvalidConfig;
use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class WorkflowRequestStatusState extends State
{
    abstract public function status(): string;

    /**
     * @throws InvalidConfig
     */
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(InProgress::class)
            ->allowTransition(InProgress::class, Approved::class)
            ->allowTransition(InProgress::class, Rejected::class);
    }
}
