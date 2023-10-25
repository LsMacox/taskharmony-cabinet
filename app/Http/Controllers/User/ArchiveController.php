<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\States\WorkflowStatus\Approved;
use App\Models\Workflow;
use App\Resources\UserArchiveResource;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class ArchiveController extends Controller
{
    public function __construct()
    {
    }

    public function downloadWorkflow($workflow): Response
    {
        $this->authorize('view', [UserArchiveResource::class]);

        $workflow = Workflow::approved()->whereState('state', Approved::class)->findOrFail($workflow);

        $pdf = PDF::loadView('workflow_report', ['workflow' => $workflow]);

        return $pdf->download('workflow_report.pdf');
    }
}
