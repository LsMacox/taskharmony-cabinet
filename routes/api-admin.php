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
use App\Http\Controllers\Admin\WorkflowRequestController;

Route::resource('users', UserController::class)
    ->except(['create', 'edit']);

Route::group(['prefix' => 'groups', 'name' => 'groups.'], function () {
    Route::resource('/', GroupController::class)
        ->except(['create', 'edit']);

    Route::post('/attach-user/{user}/group/{group}', [GroupController::class, 'attachUser']);
    Route::post('/detach-user/{user}/group/{group}', [GroupController::class, 'detachUser']);
});

Route::resource('workflow-requests', WorkflowRequestController::class)
    ->except(['create', 'edit']);
