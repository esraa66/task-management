<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ]);

        $credentials = $request->only(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        return $this->respondWithToken($token);
    }

    public function me()
    {
        return response()->json([
            'data' => auth()->user()
        ]);
    }

    public function logout()
    {
        auth()->logout();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

	public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'data' => [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60
            ],
            'message' => 'Login successful'
        ]);
    }

}


