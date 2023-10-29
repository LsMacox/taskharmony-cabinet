<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\GroupController;
use App\Http\Controllers\User\WorkflowController;
use App\Http\Controllers\User\UserWorkflowApprovalController;
use App\Http\Controllers\User\ArchiveController;
use App\Http\Controllers\User\NotificationController;

Route::prefix('groups')->name('groups.')->group(function () {
    Route::get('', [GroupController::class, 'index'])->name('index');
    Route::get('tree', [GroupController::class, 'tree'])->name('tree');
    Route::get('permissions', [GroupController::class, 'permissions'])->name('tree');
});

Route::resource('workflows', WorkflowController::class)->except(['create', 'edit']);

Route::get('workflows/{workflow}/approvals-count', [WorkflowController::class, 'getApprovalsCount'])
    ->name('workflows.get-approvals-count');

Route::prefix('user-workflow-approvals')->name('user.workflow.approval.')->group(function () {
    Route::post('{workflow}/approve', [UserWorkflowApprovalController::class, 'approve'])->name('approve');
    Route::post('{workflow}/reject', [UserWorkflowApprovalController::class, 'reject'])->name('reject');
});

Route::prefix('archive')->name('archive.')->group(function () {
    Route::get('workflow/{workflow}/download', [ArchiveController::class, 'downloadWorkflow'])->name('workflow.download');
});

Route::get('notifications', [NotificationController::class, 'index'])->name('notification.index');
