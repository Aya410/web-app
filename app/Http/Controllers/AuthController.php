<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Http\Requests\LoginRequest;
use Illuminate\Foundation\Auth\User;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    protected $authService;
    public function __construct(AuthService $authService1)
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
        $this->authService = $authService1;
    }


    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
     {
        return $this->authService->register($request);

    }

    public function login(LoginRequest $request)
    {
        return $this->authService->login($request);
    }


    public function logout()
    {
        return $this->authService->logout();

    }

    public function refresh()
    {
        return $this->authService->refresh();
    }





}
