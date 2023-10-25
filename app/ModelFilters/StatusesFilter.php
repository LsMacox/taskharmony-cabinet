<?php

namespace App\ModelFilters;

use App\Models\Workflow;
use Illuminate\Database\Eloquent\Builder;

trait StatusesFilter
{
    public function filterCustomStatus(Builder $builder, $value)
    {
        $value = strtolower($value);

        $status = Workflow::MAP_STRING_STATUSES[$value] ?? null;

        if (!is_null($status)) {
            return $builder->where('status', $status);
        }
    }
}
