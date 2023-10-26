<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\WorkflowResource;
use App\Models\UserWorkflowApproval;
use App\Models\Workflow;
use App\Repository\WorkflowRepository;
use App\Resources\UserWorkflowResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use App\Http\Requests\User\WorkflowRequest;
use Illuminate\Http\JsonResponse;

class WorkflowController extends Controller
{
    public function __construct(public WorkflowRepository $repository)
    {
        $this->authorizeResource(UserWorkflowResource::class);
    }

    public function index(): AnonymousResourceCollection
    {
        $workflows = $this->repository->getUserWorkflowBuilder(auth()->user())
            ->filter()
            ->paginate();

        return WorkflowResource::collection($workflows);
    }

    public function store(WorkflowRequest $request): JsonResponse|WorkflowResource
    {
        $workflow = Workflow::create($request->validated());

        return new WorkflowResource($workflow);
    }

    public function update(WorkflowRequest $request, $workflow): JsonResponse|WorkflowResource
    {
        $workflow = $this->repository->getUserWorkflowBuilder(auth()->user())->findOrFail($workflow);

        if (!$this->checkUserGroupPermission($workflow->group_id)) {
            return response()->json(['error' => 'You do not have permission to update in this group.'], 403);
        }

        $workflow->update($request->validated());

        return new WorkflowResource($workflow);
    }

    public function show($workflow): WorkflowResource
    {
        $workflow = $this->repository->getUserWorkflowBuilder(auth()->user())->findOrFail($workflow);

        return new WorkflowResource($workflow);
    }

    public function destroy($workflow): JsonResponse|Response
    {
        $workflow = $this->repository->getUserWorkflowBuilder(auth()->user())->findOrFail($workflow);

        if (!$this->checkUserGroupPermission($workflow->group_id)) {
            return response()->json(['error' => 'You do not have permission to update in this group.'], 403);
        }

        $workflow->delete();

        return response()->noContent();
    }

    public function getApprovalsCount($workflow): JsonResponse
    {
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
