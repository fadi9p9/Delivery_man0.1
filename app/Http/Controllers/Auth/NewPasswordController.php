<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class NewPasswordController extends Controller
{
    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        // Validate the request
        $validatedData = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['nullable', 'email', 'required_without:phoneNumber'],
            'phoneNumber' => ['nullable', 'string', 'required_without:email', 'regex:/^\+?[0-9]{10,15}$/'],
            'password' => [
                'required',
                'confirmed',
                PasswordRule::defaults()
            ],
        ]);

        // Retrieve user based on email or phoneNumber
        $user = User::where('email', $request->input('email'))
                    ->orWhere('phoneNumber', $request->input('phoneNumber'))
                    ->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'identifier' => [__('We can\'t find a user with that email address or phoneNumber number.')],
            ]);
        }

        // Verify the reset token
        $status = Password::getRepository()->exists($user, $request->input('token'));

        if (!$status) {
            throw ValidationException::withMessages([
                'token' => [__('Invalid or expired token.')],
            ]);
        }

        // Reset the password
        $user->forceFill([
            'password' => Hash::make($validatedData['password']),
            'remember_token' => Str::random(60),
        ])->save();

        // Fire the PasswordReset event
        event(new PasswordReset($user));

        // Return success response
        return response()->json([
            'status' => __('Password has been reset successfully.'),
        ]);
    }
}
