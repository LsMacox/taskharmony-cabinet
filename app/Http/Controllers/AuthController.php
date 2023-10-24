<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
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

            return response()->json(
                [
                'message' => 'Successfully created user!',
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

        return response()->json(
            [
            'accessToken' => $token,
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
        $policies = Gate::policies();
        $abilities = [];

        foreach ($policies as $model => $policy) {
            $policyMethods = get_class_methods(new $policy);

            foreach ($policyMethods as $method) {
                $p_method = strtolower($method);
                $p_model = ucfirst(strtolower($model));

                if ($user->can($p_method . '.' . $p_model)) {
                    $abilities[] = [
                        'action' => $p_method,
                        'subject' => $p_model,
                    ];
                }
            }
        }

        return $abilities;
    }
}
