<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\GroupResource;
use App\Http\Resources\GroupTreeResource;
use App\Resources\UserGroupResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $this->authorize('view', [UserGroupResource::class]);

        $groups = auth()->user()->groups()->filter()->get();

        return GroupResource::collection($groups);
    }

    public function tree(): AnonymousResourceCollection
    {
        $this->authorize('view', [UserGroupResource::class]);

        $rootGroups = auth()->user()->groups()
            ->filter()
            ->with('children')
            ->whereNull('parent_id')
            ->orderBy('is_department', 'desc')
            ->get();

        return GroupTreeResource::collection($rootGroups);
    }

    public function permissions(): JsonResource
    {
        $this->authorize('view', [UserGroupResource::class]);

        $permissions = auth()->user()->groups()?->withPivot('permissions')
            ->get()
            ->map(function ($group) {
                return [
                    'id' => $group['id'],
                    'name' => $group['name'],
                    'permissions' => $group->pivot['permissions'],
                ];
            });

        return new JsonResource($permissions);
    }
}
