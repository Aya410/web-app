<?php

namespace App\Services;

use Exception;
use App\Repositories\AuthRepository;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RegisterRequest;
use App\Interfaces\AuthenticationRepository;
use Illuminate\Auth\AuthenticationException;

class AuthService
{
    protected $authRepo;

    public function __construct(AuthRepository $AuthRepository1)
    {
        $this->authRepo = $AuthRepository1;
    }

    public function Register(RegisterRequest $request)
    {
        $userInfo = $request->all();
        $user = $this->authRepo->register($userInfo);

    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $token = Auth::attempt($credentials);

        if (!$token) {
            return response()->json(['message' => 'invalid info'], 400);
        }

        $user = Auth::user();
        $response = [
            'user' => $user,
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
            ]];

        return response()->json($response, 200);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }

}
