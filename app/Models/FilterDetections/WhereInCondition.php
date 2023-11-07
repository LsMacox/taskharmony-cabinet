<?php

namespace App\Models\FilterDetections;

use App\Models\FilterDetections\Query\WhereInQuery;
use eloquentFilter\QueryFilter\Detection\Contract\DefaultConditionsContract;

class WhereInCondition implements DefaultConditionsContract
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
        if (isset($params['operator']) && $field !== 'or' && count($params) > 1) {
            $method = WhereInQuery::class;
        }

        return $method ?? null;
    }
}
