<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;

class LoginController extends Controller
{
    /**
     * Login
     */
    public function __invoke(LoginRequest $request)
    {
        if (! $token = auth()->attempt($request->validated())) {
            return response()->json(['error' => 'invalid credentials!'], 422);
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60, // @phpstan-ignore-line
        ]);
    }
}
