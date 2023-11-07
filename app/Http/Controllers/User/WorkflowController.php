<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\WorkflowResource;
use App\Http\Resources\WorkflowShowResource;
use App\Jobs\ApprovalCleanJob;
use App\Models\UserWorkflowApproval;
use App\Models\Workflow;
use App\Repository\WorkflowRepository;
use App\Resources\UserWorkflowResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use App\Http\Requests\User\WorkflowRequest;
use Illuminate\Http\JsonResponse;

class WorkflowController extends Controller
{
    public function __construct(public WorkflowRepository $repository)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', [UserWorkflowResource::class]);

        $perPage = $request->input('perpage', 15);
        $workflows = $this->repository->getUserWorkflowBuilder(auth()->user())
            ->ignoreRequest(['perpage'])
            ->filter()
            ->paginate($perPage);

        return WorkflowResource::collection($workflows);
    }

    public function store(WorkflowRequest $request): JsonResponse|WorkflowResource
    {
        $this->authorize('create', [UserWorkflowResource::class]);

        $workflow = Workflow::create($request->validated());

        return new WorkflowResource($workflow);
    }

    public function update(WorkflowRequest $request, $workflow): JsonResponse|WorkflowResource
    {
        $this->authorize('update', [UserWorkflowResource::class]);

        $workflow = $this->repository->getUserWorkflowBuilder(auth()->user())->findOrFail($workflow);

        if (!$this->checkUserGroupPermission($workflow->group_id)) {
            return response()->json(['error' => 'You do not have permission to update in this group.'], 403);
        }

        $workflow->update($request->validated());

        ApprovalCleanJob::dispatch($workflow);

        return new WorkflowResource($workflow);
    }

    public function show($workflow): WorkflowShowResource
    {
        $this->authorize('view', [UserWorkflowResource::class]);

        $workflow = $this->repository->getUserWorkflowBuilder(auth()->user())->findOrFail($workflow);

        return new WorkflowShowResource($workflow);
    }

    public function destroy($workflow): JsonResponse|Response
    {
        $this->authorize('delete', [UserWorkflowResource::class]);

        $workflow = $this->repository->getUserWorkflowBuilder(auth()->user())->findOrFail($workflow);

        if (!$this->checkUserGroupPermission($workflow->group_id)) {
            return response()->json(['error' => 'You do not have permission to update in this group.'], 403);
        }

        $workflow->delete();

        return response()->noContent();
    }

    public function getApprovalsCount($workflow): JsonResponse
    {
        $this->authorize('view', [UserWorkflowResource::class]);

        $workflow = $this->repository->getUserWorkflowBuilder(auth()->user())->findOrFail($workflow);

        $counts = $this->repository->getCounts($workflow);

        return response()->json($counts);
    }

    protected function checkUserGroupPermission(int $groupId): bool
    {
        $user_group = auth()->user()
            ->groups()
            ->where('groups.id', $groupId)
            ->withPivot('permissions')
            ->first();

        if (!$user_group ||
            !$user_group->pivot['permissions'] ||
            !in_array('create', $user_group->pivot['permissions'])) {
            return false;
        }

        return true;
    }
}
