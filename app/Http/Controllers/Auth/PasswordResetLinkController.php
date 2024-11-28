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
            $status = Password::sendResetLink(
                $request->only('email')
            );

            if ($status === Password::RESET_LINK_SENT) {
                return response()->json([
                    'message' => __('Password reset link sent successfully.'),
                ], 200);
            }

            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
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

            return response()->json([
                'message' => __('Password has been reset successfully.'),
            ], 200);
        }

        throw ValidationException::withMessages([
            'error' => [__('Invalid request.')],
        ]);
    }
}
