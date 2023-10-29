<?php

namespace App\ModelFilters;

use App\Models\Workflow;
use Illuminate\Database\Eloquent\Builder;

trait GroupExcludeChildrenOf
{
    public function filterCustomExcludeChildrenOf(Builder $builder, array $parentIds): Builder
    {
        $parentIds = is_array($parentIds) ? $parentIds : [$parentIds];

        $childIds = $this->whereIn('parent_id', $parentIds)->pluck('id')->toArray();

        while (!empty($childIds)) {
            $newChildIds = $this->whereIn('parent_id', $childIds)->pluck('id')->toArray();
            $childIds = array_merge($childIds, $newChildIds);
            $childIds = array_unique($childIds);
        }

        return $builder->whereNotIn('id', $childIds);
    }
}
