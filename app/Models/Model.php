<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Spatie\ModelStates\HasStates;
use Spatie\ModelStates\State;

class Model extends BaseModel
{
    use HasStates;

    public function stateOptions($stateOptions, Model $model): array
    {
        if (is_subclass_of($stateOptions, State::class)) {
            $stateOptions = $stateOptions::all();
        }

        if (is_array($stateOptions)) {
            $stateOptions = collect($stateOptions);
        }

        return $this->options($stateOptions->mapWithKeys(function (string $className) use ($model) {
            /**
             * @var State $className
             * @var State $state
             */
            $state = new $className($model);

            $label = method_exists($state, 'label')
                ? $state->label()
                : $state::getMorphClass();

            return [$className::getMorphClass() => $label];
        }));
    }
}
