<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\GroupResource;
use App\Http\Resources\GroupTreeResource;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class UserGroupController extends Controller
{
    public function index(User $user, Request $request): AnonymousResourceCollection
    {
        $this->authorize('view', [Group::class]);

        $groups = $user->groups()->filter()->get();

        return GroupResource::collection($groups);
    }

    public function tree(User $user): AnonymousResourceCollection
    {
        $this->authorize('view', [Group::class]);

        $rootGroups = $user->groups()
            ->filter()
            ->with('children')
            ->whereNull('parent_id')
            ->orderBy('is_department', 'desc')
            ->get();

        return GroupTreeResource::collection($rootGroups);
    }

    public function updateGroupPermission(Request $request, User $user, Group $group)
    {
        $this->authorize('create', [Group::class]);

        $this->validate($request, [
            'permissions' => ['present', 'nullable', 'array'],
            'permissions.*' => ['nullable', 'string', Rule::in(GroupUser::PERMISSIONS)],
        ]);

        $isUpdated = $user->groups()
            ->updateExistingPivot($group->id, ['permissions' => $request->input('permissions')]);

        if (!$isUpdated) {
            return response()
                ->json(['The specified user-group relation either does not exist or has been successfully updated.'])
                ->setStatusCode(404);
        }

        return response()->json(['Pivot table entry successfully updated.']);
    }

    public function getGroupPermission(User $user, Group $group): JsonResponse
    {
        $this->authorize('view', [Group::class]);

        $group = $user->groups()->withPivot('permissions')->findOrFail($group->id);

        return response()->json([
            'permissions' => $group->pivot->permissions,
        ]);
    }

    public function syncUsers(Request $request, Group $group): JsonResponse
    {
        $this->authorize('create', [Group::class]);

        $request->validate([
            'user_ids' => 'nullable|exists:users,id',
        ]);

        $group->users()->sync($request->input('user_ids', []));

        return response()->json(['message' => 'User attached to group successfully']);
    }
}
