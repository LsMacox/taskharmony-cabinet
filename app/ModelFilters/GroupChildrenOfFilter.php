<?php

namespace App\ModelFilters;

use Illuminate\Database\Eloquent\Builder;

trait GroupChildrenOfFilter
{
    public function filterCustomInclude_children_of(Builder $builder, array $groupIds): Builder
    {
        $allGroupIds = $this->getChildrenIds($groupIds);

        return $builder->whereIn('groups.id', $allGroupIds);
    }

    public function filterCustomExclude_children_of(Builder $builder, array $groupIds): Builder
    {
        $parentIds = $this->whereIn('id', $groupIds)->pluck('parent_id')->toArray();

        return $builder->whereNotIn('groups.id', array_merge($groupIds, $parentIds, $this->getChildrenIds($groupIds)));
    }

    private function getChildrenIds(array $groupIds): array
    {
        $groupIds = is_array($groupIds) ? $groupIds : [$groupIds];

        $allGroupIds = $this->whereIn('parent_id', $groupIds)->pluck('id')->toArray();
        $childIds = $allGroupIds;

        while (!empty($childIds)) {
            $childIds = $this->whereIn('parent_id', $childIds)->pluck('id')->toArray();
            $allGroupIds = array_merge($allGroupIds, $childIds);
            $allGroupIds = array_unique($allGroupIds);
        }

        return $allGroupIds;
    }
}
