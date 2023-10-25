<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GroupRequest;
use App\Http\Resources\GroupResource;
use App\Http\Resources\GroupTreeResource;
use App\Http\Resources\UserResource;
use App\Models\Group;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class GroupController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Group::class);
    }

    public function index(): AnonymousResourceCollection
    {
        $groups = Group::filter()->paginate();

        return GroupResource::collection($groups);
    }

    public function tree(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', [Group::class]);

        $rootGroups = Group::filter()
            ->whereNull('parent_id')
            ->with('children')
            ->orderBy('is_department', 'desc')
            ->get();

        return GroupTreeResource::collection($rootGroups);
    }

    public function store(GroupRequest $request): GroupResource
    {
        $group = Group::create($request->validated());

        return new GroupResource($group);
    }

    public function show(Group $group): GroupResource
    {
        return new GroupResource($group);
    }

    public function update(GroupRequest $request, Group $group): GroupResource
    {
        $group->update($request->validated());

        return new GroupResource($group);
    }

    public function destroy(Group $group): Response
    {
        $group->delete();

        return response()->noContent();
    }

    public function getAttachedUsers(Group $group): AnonymousResourceCollection
    {
        $this->authorize('view', [Group::class]);

        $users = $group->users()->get();

        return UserResource::collection($users);
    }
}
