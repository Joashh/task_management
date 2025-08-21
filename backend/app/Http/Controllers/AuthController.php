<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    // Register user
    public function register(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'in:user,admin',
        ])->validate();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'] ?? 'user',
        ]);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
        ], 201);
    }

    // Login user (cookie-based)
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        if (!Auth::attempt($validated)) {
            throw ValidationException::withMessages([
                'credentials' => 'Sorry, email or password is incorrect.',
            ]);
        }

        // Regenerate session to prevent fixation
        $request->session()->regenerate();

        return response()->json([
            'message' => 'Login successful',
            'user' => Auth::user(),
        ]);
    }

    // Logout user
    public function logout(Request $request)
{
    // Laravel will invalidate the session and regenerate token
    auth()->logout();                    // logout user
    $request->session()->invalidate();   // invalidate session
    $request->session()->regenerateToken(); // prevent CSRF reuse

    return response()->json(['message' => 'Logged out']);
}


    // Get authenticated user
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
