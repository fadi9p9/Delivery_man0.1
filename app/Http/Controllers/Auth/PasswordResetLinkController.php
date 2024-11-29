<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class PasswordResetLinkController extends Controller
{
    /**
     * Handle a password reset request.
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'email' => ['nullable', 'email', 'required_without:phoneNumber'],
            'phoneNumber' => ['nullable', 'string', 'required_without:email', 'regex:/^\+?[0-9]{10,15}$/'],
            'password' => ['nullable', 'confirmed', 'required_with:phoneNumber'],
        ]);

        // Handle case for email reset
        if ($request->email) {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                throw ValidationException::withMessages([
                    'email' => [__('User not found.')],
                ]);
            }

            // Generate token manually for testing
            $token = Password::getRepository()->create($user);

            return response()->json([
                'message' => __('Password reset link sent successfully.'),
                'token' => $token, // Include the token for testing
            ], 200);
        }

        // Handle case for phone number reset
        if ($request->phoneNumber) {
            $user = User::where('phoneNumber', $request->phoneNumber)->first();

            if (!$user) {
                throw ValidationException::withMessages([
                    'phoneNumber' => [__('User not found.')],
                ]);
            }

            // Update password directly
            $user->forceFill([
                'password' => Hash::make($request->password),
            ])->save();

            // Generate a new token for the user for reference
            $token = Password::getRepository()->create($user);

            return response()->json([
                'message' => __('Password has been reset successfully.'),
                'token' => $token, // Include the token for testing
            ], 200);
        }

        throw ValidationException::withMessages([
            'error' => [__('Invalid request.')],
        ]);
    }
}
