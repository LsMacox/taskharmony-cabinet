<?php

namespace App\ModelFilters;

use App\Models\Group;
use App\Models\Workflow;
use Illuminate\Database\Eloquent\Builder;

trait UsersExcludeFromGroups
{
    public function filterCustomExcludeFromGroups(Builder $builder, array $parentIds): Builder
    {
        $parentIds = is_array($parentIds) ? $parentIds : [$parentIds];

        $childGroupIds = (new Group)->newQuery()->filterCustomExcludeChildrenOf($parentIds)->pluck('id')->toArray();

        $allGroupIds = array_merge($parentIds, $childGroupIds);

        return $builder->whereDoesntHave('groups', function ($query) use ($allGroupIds) {
            $query->whereIn('id', $allGroupIds);
        });
    }
}
