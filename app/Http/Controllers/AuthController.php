<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // POST /api/login
    public function login(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string',
            'password' => 'required',
        ]);

        // check if user exists by username
        $user = User::where('username', $validated['username'])->first();

        // check pwd
        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['The provided credentials are incorrect.'],
            ]);
        }

        // create token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'access_token' => $token,
            'user' => $user,
            'role' => $user->role,
        ]);
    }

    // POST /api/users
    public function register(Request $request)
    {
        if ($request->user()->role !== UserRole::SUPERUSER) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $validated = $request->validate([
            'passport_number' => 'required|string|unique:users,passport_number',
            'username' => 'required|string|unique:users,username',
            'name' => 'required|string',
            'password' => 'required|string|min:6',
            'role' => 'required|in:superuser,supervisor',
        ]);

        $user = User::create([
            'passport_number' => $validated['passport_number'],
            'username' => $validated['username'],
            'name' => $validated['name'],
            'password' => Hash::make($validated['password']),
            'role' => UserRole::from($validated['role']),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User registered successfully',
            'user' => $user,
        ], 201);
    }

    // GET /api/users
    public function index(Request $request)
    {
        if ($request->user()->role !== UserRole::SUPERUSER) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 403); 
        }

        $users = User::all();

        return response()->json([
            'status' => true,
            'users' => $users,
        ]);
    }

    // POST /api/logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logout successful',
        ]);
    }
}
