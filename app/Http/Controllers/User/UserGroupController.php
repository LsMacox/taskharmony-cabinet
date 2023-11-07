<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\GroupResource;
use App\Http\Resources\GroupTreeResource;
use App\Jobs\ApprovalCleanJob;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\User;
use App\Resources\UserGroupResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class UserGroupController extends Controller
{
    public function updateGroupPermission(Request $request, int $group)
    {
        $this->authorize('create', [UserGroupResource::class]);

        $this->validate($request, [
            'permissions' => ['present', 'nullable', 'array'],
            'permissions.*' => ['nullable', 'string', Rule::in(GroupUser::PERMISSIONS)],
        ]);

        $isUpdated = auth()->user()->groups()
            ->updateExistingPivot($group, ['permissions' => $request->input('permissions')]);

        if (!$isUpdated) {
            return response()
                ->json(['The specified user-group relation either does not exist or has been successfully updated.'])
                ->setStatusCode(404);
        }

        return response()->json(['Pivot table entry successfully updated.']);
    }

    public function getGroupPermission(int $group): JsonResponse
    {
        $this->authorize('create', [UserGroupResource::class]);

        $group = auth()->user()->groups()->withPivot('permissions')->findOrFail($group);

        return response()->json([
            'permissions' => $group->pivot->permissions,
        ]);
    }

    public function syncUsers(Request $request, int $group): JsonResponse
    {
        $this->authorize('create', [UserGroupResource::class]);

        $group = auth()->user()->groups()->findOrFail($group);

        $request->validate([
            'user_ids' => 'nullable|exists:users,id',
        ]);

        foreach ($group->workflows as $workflow) {
            ApprovalCleanJob::dispatch($workflow);
        }

        $group->users()->sync($request->input('user_ids', []));

        return response()->json(['message' => 'User attached to group successfully']);
    }
}
