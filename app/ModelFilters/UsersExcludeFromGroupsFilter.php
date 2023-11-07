<?php

namespace App\ModelFilters;

use App\Models\Group;
use App\Models\Workflow;
use Illuminate\Database\Eloquent\Builder;

trait UsersExcludeFromGroupsFilter
{
    public function filterCustomExclude_from_groups(Builder $builder, array $parentIds): Builder
    {
        $parentIds = is_array($parentIds) ? $parentIds : [$parentIds];

        $childGroupIds = (new Group)
            ->filterCustomExclude_children_of((new Group)->newQuery(), $parentIds)->pluck('id')->toArray();

        $allGroupIds = array_merge($parentIds, $childGroupIds);

        return $builder->whereDoesntHave('groups', function ($query) use ($allGroupIds) {
            $query->whereIn('groups.id', $allGroupIds);
        });
    }
}
