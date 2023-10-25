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
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\GroupController;
use App\Http\Controllers\Admin\UserGroupController;
use App\Http\Controllers\Admin\WorkflowController;

Route::resource('users', UserController::class)
    ->except(['create', 'edit']);

Route::name('groups.')->prefix('groups')->group(function () {
    Route::get('tree', [GroupController::class, 'tree'])->name('tree');

    Route::post('get-attached-users/{group}', [GroupController::class, 'getAttachedUsers'])
        ->name('get-attached-user');
});

Route::name('users.groups.')->group(function () {
    Route::post('users/{user}/groups', [UserGroupController::class, 'index'])
        ->name('index');
    Route::post('users/{user}/groups/tree', [UserGroupController::class, 'tree'])
        ->name('tree');
    Route::post('users/{user}/groups/{group}/update-group-permissions', [UserGroupController::class, 'updateGroupPermission'])
        ->name('update-group-permission');
    Route::post('users/{user}/groups/{group}/attach-group', [UserGroupController::class, 'attachGroup'])
        ->name('attach-group');
    Route::post('users/{user}/groups/{group}/detach-group', [UserGroupController::class, 'detachGroup'])
        ->name('detach-group');
});

Route::get('workflows/{workflow}/approvals-count', [WorkflowController::class, 'getApprovalsCount'])
    ->name('workflows.get-approvals-count');
Route::resource('groups', GroupController::class)->except(['create', 'edit']);
Route::resource('workflows', WorkflowController::class)->except(['create', 'edit']);
