<?php

namespace App\Http\Services;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthService {

    public function createUser(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(),
            [
                'name' => 'required|string',
                'email' => 'required|email|unique:users,email',
                'password' => 'required'
            ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'message' => 'Validation error',
                    'errors' => $validateUser->errors()
                ], 422);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'message' => 'User created successfully',
                'token' => $user->createToken('API_TOKEN')->plainTextToken
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'error' => 'Cannot create user! Something went wrong.'
            ], 500);
        }
    }

    public function loginUser(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(),
            [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'message' => 'Validation error',
                    'errors' => $validateUser->errors()
                ], 422);
            }

            if (!Auth::attempt($request->only(['email', 'password']))) {
                return response()->json([
                    'message' => 'Invalid credentials'
                ], 401);
            }

            $user = User::where('email', $request->email)->first();

            return response()->json([
                'message' => 'User logged in successfully',
                'token' => $user->createToken('API_TOKEN')->plainTextToken
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'error' => 'Cannot log in! Something went wrong.'
            ], 500);
        }
    }
}
