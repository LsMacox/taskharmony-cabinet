<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class);
    }

    public function index(): AnonymousResourceCollection
    {
        $users = User::filter()->with('roles')->paginate();

        return UserResource::collection($users);
    }

    public function store(UserRequest $request): UserResource
    {
        $user = User::create($request->validated());

        if ($request->filled('roles')) {
            $user->assignRole($request->input('roles'));
        }

        return new UserResource($user);
    }

    public function show(User $user): UserResource
    {
        return new UserResource($user);
    }

    public function update(UserRequest $request, User $user): UserResource
    {
        $user->update($request->validated());

        if ($request->filled('roles')) {
            $user->assignRole($request->input('roles'));
        }

        return new UserResource($user);
    }

    public function destroy(User $user): Response
    {
        $user->delete();

        return response()->noContent();
    }
}
