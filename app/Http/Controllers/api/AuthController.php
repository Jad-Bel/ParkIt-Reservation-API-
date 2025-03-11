<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Services\AuthService;
use App\Http\Controllers\Controller;
use Exception;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(Request $request)
    {
        return $this->authService->createUser($request);
    }

    public function login(Request $request)
    {
        return $this->authService->loginUser($request);
    }

    public function logout(Request $request)
    {
        // try {
            // $request->user()->currentAccessToken()->delete();
            $request->user()->tokens->each(function ($token) {
                $token->delete();
            });

            return response()->json([
                'message' => 'User logged out successfully'
            ], 200);

        // } catch (Exception $e) {
        //     return response()->json([
        //         'error' => 'Cannot log out! Something went wrong.'
        //     ], 500);
        // }
    }
}
