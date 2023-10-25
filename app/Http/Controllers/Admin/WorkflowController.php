<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\WorkflowResource;
use App\Models\States\WorkflowStatus\WorkflowStatusState;
use App\Models\UserWorkflowApproval;
use App\Models\Workflow;
use App\Repository\WorkflowRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Http\Requests\Admin\WorkflowRequest;

class WorkflowController extends Controller
{
    public function __construct(public WorkflowRepository $repository)
    {
        $this->authorizeResource(Workflow::class);
    }

    public function index(): AnonymousResourceCollection
    {
        $workflows = Workflow::withAnyStatus()
            ->filter()
            ->paginate();

        return WorkflowResource::collection($workflows);
    }

    public function store(WorkflowRequest $request): WorkflowResource
    {
        $workflow = Workflow::create($this->prepareFill($request));

        $workflow->markApproved();
        $workflow->refresh();

        return new WorkflowResource($workflow);
    }

    public function update(WorkflowRequest $request, $workflow): WorkflowResource
    {
        $workflow = Workflow::withAnyStatus()->findOrFail($workflow);
        $workflow->update($this->prepareFill($request));

        if ($request->filled('state')) {
            $workflow->state->transitionTo(WorkflowStatusState::all()[$request->input('state')]);
        }

        return new WorkflowResource($workflow);
    }

    public function show($workflow): WorkflowResource
    {
        $workflow = Workflow::withAnyStatus()->findOrFail($workflow);

        return new WorkflowResource($workflow);
    }

    public function destroy($workflow): Response
    {
        $workflow = Workflow::withAnyStatus()->findOrFail($workflow);

        $workflow->delete();

        return response()->noContent();
    }

    public function getApprovalsCount(Workflow $workflow): JsonResponse
    {
        $allAsGroupsIds = $this->repository->getAllGroupIdsFromApprovalSequence($workflow);

        $userWorkflowGroups = UserWorkflowApproval::with('group')
            ->where('workflow_id', $workflow->id)
            ->whereNotNull('group_id')
            ->get();

        $groupCountList = [];
        $userCount = UserWorkflowApproval::where('workflow_id')->whereNull('group_id')->count();

        foreach ($allAsGroupsIds as $groupsId) {
            $userWorkflowGroup = $userWorkflowGroups->where('group_id', $groupsId)->first()->group;
            $groupCountList[] = [
                'id' => $groupsId,
                'name' => $userWorkflowGroup->name,
                'count' => $userWorkflowGroups->where('group_id', $groupsId)->count(),
            ];
        }

        $totalGroupCount = array_reduce($groupCountList, function ($carry, $item) {
            return $carry + $item['count'];
        }, 0);

        return response()->json([
            'userCount' => $userCount,
            'groupCountList' => $groupCountList,
            'total' => $userCount + $totalGroupCount,
        ]);
    }

    protected function prepareFill(Request $request): array
    {
        $prepared = $request->validated();

        if ($request->filled('status')) {
            $prepared = array_merge($prepared, [
                'status' => Workflow::MAP_STRING_STATUSES[$request->input('status')],
            ]);
        }

        return $prepared;
    }
}
