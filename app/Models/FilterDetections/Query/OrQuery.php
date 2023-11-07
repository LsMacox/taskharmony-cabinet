<?php

namespace App\Models\FilterDetections\Query;

use eloquentFilter\QueryFilter\Queries\BaseClause;
use Illuminate\Database\Eloquent\Builder;

class OrQuery extends BaseClause
{
    /**
     * @param $query
     *
     * @return Builder
     */
    public function apply($query): Builder
    {
        $builder = $query;

        foreach ($this->values as $key => $values) {
            $operator = $values['operator'] ?: '=';
            $key_values = collect($values)->forget(['operator', 'value'])->toArray();
            $value = $values['value'] ?? null;

            if ($value) {
                $builder = $builder->orWhere($key, $operator, $value);
            }

            if (!empty($key_values)) {
                $builder = match ($operator) {
                    '!=' => $query->orWhereNotIn($key, $key_values),
                    '=' => $query->orWhereIn($key, $key_values),
                };
            }
        }

        return $builder;
    }
}
