<?php

namespace App\Models\FilterDetections\Query;

use eloquentFilter\QueryFilter\Queries\BaseClause;
use Illuminate\Database\Eloquent\Builder;

class WhereLikeQuery extends BaseClause
{
    /**
     * @param $query
     *
     * @return Builder
     */
    public function apply($query): Builder
    {
        $operator = $this->values['operator'];
        $value = $this->values['value'];

        return $query->where($this->filter, $operator, $value);
    }
}
