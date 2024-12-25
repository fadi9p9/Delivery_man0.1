<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users'],
            'phoneNumber' => ['nullable', 'string', 'max:15', 'unique:users'],
            'lastName' => [ 'nullable','string', 'max:255'],
            'password' => ['required', Rules\Password::defaults()],

        ]);

        if (!$request->email && !$request->phoneNumber) {
            return response()->json(['message' => 'Email or Phone is required'], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email ? strtolower($request->email) : null,
            'phoneNumber' => $request->phoneNumber,
            'password' => Hash::make($request->password),
            'lastName' => $request->lastName,
            'role' => 'Customer',
            'location' => null,

        ]);

        event(new Registered($user));
        Auth::login($user);

        $user['token'] = $user->createToken('token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user
        ], 201);
    }
}
