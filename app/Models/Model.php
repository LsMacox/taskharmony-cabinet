<?php

namespace App\Models;

use App\Models\FilterDetections\OrCondition;
use App\Models\FilterDetections\WhereInCondition;
use App\Models\FilterDetections\WhereLikeCondition;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Spatie\ModelStates\HasStates;
use Spatie\ModelStates\State;

class Model extends BaseModel
{
    use HasStates;

    public function EloquentFilterCustomDetection(): array
    {
        return [
            WhereLikeCondition::class,
            WhereInCondition::class,
            OrCondition::class,
        ];
    }
}
