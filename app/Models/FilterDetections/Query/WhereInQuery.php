<?php

namespace App\Models\FilterDetections\Query;

use eloquentFilter\QueryFilter\Queries\BaseClause;
use Illuminate\Database\Eloquent\Builder;

class WhereInQuery extends BaseClause
{
    /**
     * @param $query
     *
     * @return Builder
     */
    public function apply($query): Builder
    {
        $operator = $this->values['operator'];
        $values = collect($this->values)->forget('operator');

        return match ($operator) {
            '!=' => $query->whereNotIn($this->filter, $values),
            '=' => $query->whereIn($this->filter, $values),
        };
    }
}
