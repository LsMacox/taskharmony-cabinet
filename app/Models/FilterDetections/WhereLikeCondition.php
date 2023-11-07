<?php

namespace App\Models\FilterDetections;

use App\Models\FilterDetections\Query\WhereInQuery;
use App\Models\FilterDetections\Query\WhereLikeQuery;
use eloquentFilter\QueryFilter\Detection\Contract\DefaultConditionsContract;

class WhereLikeCondition implements DefaultConditionsContract
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
        if (isset($params['operator']) && $field !== 'or' && in_array($params['operator'], ['like', 'ilike'])) {
            $method = WhereLikeQuery::class;
        }

        return $method ?? null;
    }
}
