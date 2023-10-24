<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GroupRequest;
use App\Http\Resources\GroupResource;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

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

    public function attachUser(User $user, Group $group): JsonResponse
    {
        $user->groups()->syncWithoutDetaching([$group->id]);

        return response()->json(['message' => 'User attached to group successfully']);
    }

    public function detachUser(User $user, Group $group): JsonResponse
    {
        $user->groups()->detach($group->id);

        return response()->json(['message' => 'User detached from group successfully']);
    }
}
