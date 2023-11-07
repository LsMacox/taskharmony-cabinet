<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Resources\UserGroupResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('create', [UserGroupResource::class]);

        $perPage = $request->input('perpage', 15);
        $users = User::ignoreRequest(['perpage'])
            ->filter()
            ->with('roles')
            ->paginate($perPage);

        return UserResource::collection($users);
    }
}
