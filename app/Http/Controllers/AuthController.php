<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $request->validate(
            [
                'name' => 'required|string',
                'email' => 'required|string|unique:users',
                'password' => 'required|string',
            ]
        );

        $user = new User(
            [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]
        );

        if ($user->save()) {
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->plainTextToken;

            $user->load('roles');

            return response()->json(
                [
                    'message' => 'Successfully created user!',
                    'user' => $user,
                    'userAbilityRules' => $this->getUserAbilities($user),
                    'accessToken' => $token,
                ],
                201
            );
        } else {
            return response()->json(['error' => 'Provide proper details']);
        }
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate(
            [
                'email' => 'required|string|email',
                'password' => 'required|string',
                'remember_me' => 'boolean',
            ]
        );

        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            return response()->json(
                [
                    'message' => 'Unauthorized',
                ],
                401
            );
        }

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->plainTextToken;

        $user->load('roles');

        return response()->json(
            [
                'accessToken' => $token,
                'user' => $user,
                'userAbilityRules' => $this->getUserAbilities($user),
                'token_type' => 'Bearer',
            ]
        );
    }

    public function user(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json(
            [
                'message' => 'Successfully logged out',
            ]
        );
    }

    public function userAbilities(Request $request): JsonResponse
    {
        return response()->json(['userAbilityRules' => $this->getUserAbilities($request->user())]);
    }

    protected function getUserAbilities(User $user): array
    {
        $abilities = [];
        $role_permissions = $user->roles()->first()?->permissions->pluck('name')->toArray() ?? [];
        $user_permissions = $user->permissions()->pluck('name')->toArray();
        $all_permissions = array_unique(array_merge($role_permissions, $user_permissions));

        foreach ($all_permissions as $permission) {
            list($action, $subject) = explode('.', $permission);

            $abilities[] = [
                'action' => $action,
                'subject' => $subject,
            ];
        }

        return $abilities;
    }
}
