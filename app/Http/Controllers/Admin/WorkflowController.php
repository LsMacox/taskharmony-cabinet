<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\WorkflowResource;
use App\Models\States\WorkflowStatus\WorkflowStatusState;
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

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = $request->input('perpage', 15);
        $workflows = Workflow::withAnyStatus()
            ->ignoreRequest(['perpage'])
            ->filter()
            ->paginate($perPage);

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

    public function getApprovalsCount($workflow): JsonResponse
    {
        $workflow = Workflow::withAnyStatus()->findOrFail($workflow);

        $counts = $this->repository->getCounts($workflow);

        return response()->json($counts);
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
