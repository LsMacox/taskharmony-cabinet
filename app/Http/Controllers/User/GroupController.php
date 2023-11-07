<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\GroupResource;
use App\Http\Resources\GroupTreeResource;
use App\Http\Resources\UserResource;
use App\Resources\UserGroupResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('create', [UserGroupResource::class]);

        $perPage = $request->input('perpage', 15);

        $groups = auth()->user()->groups()
            ->ignoreRequest(['perpage'])
            ->with([
        'users' => function ($query) {
                return $query->where('users.id', auth()->id())->withPivot('permissions');
        },
        ])
            ->filter()
            ->paginate($perPage);

        return GroupResource::collection($groups);
    }

    public function tree(): AnonymousResourceCollection
    {
        $this->authorize('create', [UserGroupResource::class]);

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
        $this->authorize('create', [UserGroupResource::class]);

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

    public function getAttachedUsers(int $group): AnonymousResourceCollection
    {
        $this->authorize('create', [UserGroupResource::class]);

        $group = auth()->user()->groups()->findOrFail($group);
        $users = $group->users()->get();

        return UserResource::collection($users);
    }
}
