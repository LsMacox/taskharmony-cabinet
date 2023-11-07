<?php

namespace App\Models\FilterDetections;

use App\Models\FilterDetections\Query\OrQuery;
use App\Models\FilterDetections\Query\WhereInQuery;
use eloquentFilter\QueryFilter\Detection\Contract\DefaultConditionsContract;

class OrCondition implements DefaultConditionsContract
{
    /**
     * @param $field
     * @param $params
     * @param $is_override_method
     *
     * @return string|null
     */
    public static function detect($field, $params, $is_override_method = false): ?string
    {
        if ($field === 'or') {
            $method = OrQuery::class;
        }

        return $method ?? null;
    }
}
