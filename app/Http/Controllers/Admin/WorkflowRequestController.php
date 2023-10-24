<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkflowRequestRequest;
use App\Http\Resources\WorkflowRequestResource;
use App\Models\WorkflowRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class WorkflowRequestController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(WorkflowRequest::class);
    }

    public function index(): AnonymousResourceCollection
    {
        $workflowRequests = WorkflowRequest::filter()
            ->with(['workflow', 'author'])
            ->paginate();

        return WorkflowRequestResource::collection($workflowRequests);
    }

    public function store(WorkflowRequestRequest $request): WorkflowRequestResource
    {
        $workflowRequest = WorkflowRequest::create($request->validated());

        return new WorkflowRequestResource($workflowRequest);
    }

    public function show(WorkflowRequest $workflowRequest): WorkflowRequestResource
    {
        return new WorkflowRequestResource($workflowRequest);
    }

    public function update(WorkflowRequestRequest $request, WorkflowRequest $workflowRequest): WorkflowRequestResource
    {
        $workflowRequest->update($request->validated());

        return new WorkflowRequestResource($workflowRequest);
    }

    public function destroy(WorkflowRequest $workflowRequest): Response
    {
        $workflowRequest->delete();

        return response()->noContent();
    }
}
