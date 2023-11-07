<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\WorkflowResource;
use App\Http\Resources\WorkflowShowResource;
use App\Jobs\ApprovalCleanJob;
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
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', [Workflow::class]);

        $perPage = $request->input('perpage', 15);
        $workflows = Workflow::withAnyStatus()
            ->ignoreRequest(['perpage'])
            ->filter()
            ->paginate($perPage);

        return WorkflowResource::collection($workflows);
    }

    public function store(WorkflowRequest $request): WorkflowResource
    {
        $this->authorize('create', [Workflow::class]);

        $workflow = Workflow::create($this->prepareFill($request));

        $workflow->markApproved();
        $workflow->refresh();

        return new WorkflowResource($workflow);
    }

    public function update(WorkflowRequest $request, int $workflow): WorkflowResource
    {
        $this->authorize('update', [Workflow::class]);

        $workflow = Workflow::withAnyStatus()->findOrFail($workflow);
        $workflow->update($this->prepareFill($request));

        if ($request->filled('state')) {
            $workflow->state->transitionTo(WorkflowStatusState::all()[$request->input('state')]);
        }

        ApprovalCleanJob::dispatch($workflow);

        return new WorkflowResource($workflow);
    }

    public function show(int $workflow): WorkflowShowResource
    {
        $this->authorize('view', [Workflow::class]);

        $workflow = Workflow::withAnyStatus()->findOrFail($workflow);

        return new WorkflowShowResource($workflow);
    }

    public function destroy(int $workflow): Response
    {
        $this->authorize('delete', [Workflow::class]);

        $workflow = Workflow::withAnyStatus()->findOrFail($workflow);

        $workflow->delete();

        return response()->noContent();
    }

    public function getApprovalsCount(int $workflow): JsonResponse
    {
        $this->authorize('view', [Workflow::class]);

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
