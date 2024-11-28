<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class PasswordResetLinkController extends Controller
{
    /**
     * Handle a password reset link request.
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Attempt to send the password reset link
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // Check the status and return appropriate response
        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => __('Password reset link sent successfully.'),
            ], 200);
        }

        // Handle failure case
        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }
}
