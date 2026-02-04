<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'min:5', 'max:100', 'unique:users'],
            'email' => ['required', 'string', 'email', 'unique:users'],
            'password' => [
                'required',
                'string',
                'confirmed:confirm',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->letters()
            ],
            'confirm' => [
                'required',
                'string',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->letters()
            ],
        ]);

        $user = new User([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->save();

        return response()->json(['message' => 'Registration successful']);
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt(['username' => $data['username'], 'password' => $data['password']])) {
            $request->session()->regenerate();

            return response()->json(['user' => $request->user()]);
        }

        return response()->json([
            'errors' => [
                'username' => 'Invalid username',
                'password' => 'Invalid password'
            ]
        ], 422);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function me(): JsonResponse
    {
        return response()->json(['user' => Auth::user()]);
    }
}
