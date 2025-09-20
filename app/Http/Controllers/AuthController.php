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
            return $this->error('Invalid credentials', 401);
        }

        return $this->respondWithToken($token);
    }

    public function me()
    {
        return $this->success(auth()->user(), 'User profile retrieved successfully');
    }

    public function logout()
    {
        auth()->logout();

        return $this->success(null, 'Successfully logged out');
    }

	public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token)
    {
        $tokenData = [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ];

        return $this->success($tokenData, 'Login successful');
    }

}


