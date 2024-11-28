<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class PasswordResetLinkController extends Controller
{
    
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'nullable|email',
            'phoneNumber' => 'nullable|numeric|string:10,15',  
        ]);

        $user = null;

        if ($request->email) {
            $user = User::where('email', $request->email)->first();
        } elseif ($request->phoneNumber) {
            $user = User::where('phoneNumber', $request->phoneNumber)->first();
        }

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['User not found.'],
            ]);
        }

        if ($user->email) {
            $status = Password::sendResetLink(
                $request->only('email')
            );

            if ($status === Password::RESET_LINK_SENT) {
                return response()->json(['message' => __($status)], 200);
            }
        } elseif ($user->phoneNumber) {
            $token = $user->createToken('reset-token')->plainTextToken;
            $resetLink = url('password/reset/' . $token);

            
            // $this->sendResetLinkViaPhone($user->phoneNumber, $resetLink);
            return response()->json([
                'token' => $token,
                'message' => 'Reset link sent via WhatsApp/Telegram'], 200);
        }

        throw ValidationException::withMessages([
            'email' => ['Unable to send reset link.'],
        ]);
    }

}