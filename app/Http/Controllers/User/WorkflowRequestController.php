<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkflowRequestRequest;
use App\Http\Resources\WorkflowRequestResource;
use App\Models\WorkflowRequest;
use App\Resources\UserWorkflowRequestResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class WorkflowRequestController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(UserWorkflowRequestResource::class);
    }

    public function index(): AnonymousResourceCollection
    {
        $workflowRequests = WorkflowRequest::filter()
            ->onlyForAuth()
            ->with(['workflow', 'author'])
            ->paginate();

        return WorkflowRequestResource::collection($workflowRequests);
    }

    public function store(WorkflowRequestRequest $request): WorkflowRequestResource
    {
        $workflowRequest = WorkflowRequest::create($request->validated());

        return new WorkflowRequestResource($workflowRequest);
    }

    public function show($workflowRequest): WorkflowRequestResource
    {
        $workflowRequest = WorkflowRequest::onlyForAuth()->findOrFail($workflowRequest);

        return new WorkflowRequestResource($workflowRequest);
    }

    public function update(WorkflowRequestRequest $request, $workflowRequest): WorkflowRequestResource
    {
        $workflowRequest = WorkflowRequest::onlyForAuth()->findOrFail($workflowRequest);

        $workflowRequest->update($request->validated());

        return new WorkflowRequestResource($workflowRequest);
    }

    public function destroy($workflowRequest): Response
    {
        $workflowRequest = WorkflowRequest::onlyForAuth()->findOrFail($workflowRequest);

        $workflowRequest->delete();

        return response()->noContent();
    }
}
