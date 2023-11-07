<?php

namespace App\Models\States\WorkflowStatus;

use Spatie\ModelStates\Exceptions\InvalidConfig;
use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class WorkflowStatusState extends State
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
            ->allowTransition(InProgress::class, Rejected::class)
            ->allowTransition(Rejected::class, Returned::class)
            ->allowTransition(Returned::class, Approved::class)
            ->allowTransition(Approved::class, Returned::class)
            ->allowTransition(Returned::class, Rejected::class);
    }
}
