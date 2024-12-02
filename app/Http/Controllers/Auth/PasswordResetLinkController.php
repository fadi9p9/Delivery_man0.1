<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class PasswordResetLinkController extends Controller
{
    /**
     * Handle a password reset request (sending the token).
     */
    public function store(Request $request)
    {
        // Validate the input (email or phone number)
        $request->validate([
            'email' => ['nullable', 'email', 'required_without:phoneNumber'],
            'phoneNumber' => ['nullable', 'string', 'required_without:email', 'regex:/^\+?[0-9]{10,15}$/'],
        ]);

        // Handle case for email reset
        if ($request->email) {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                throw ValidationException::withMessages([
                    'email' => [__('User not found.')],
                ]);
            }

            // Generate reset token
            $token = Password::getRepository()->create($user);

            // Send the email
            \Illuminate\Support\Facades\Mail::to($user->email)->send(
                new \App\Mail\TestMail($token)
            );

            return response()->json([
                'message' => __('Password reset link sent successfully to email.'),
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

            // Generate reset token
            $token = Password::getRepository()->create($user);

            // Send SMS
            $this->sendSms($user->phoneNumber, __('Your reset token is: ') . $token);

            return response()->json([
                'message' => __('Password reset token sent successfully via SMS.'),
            ], 200);
        }

        throw ValidationException::withMessages([
            'error' => [__('Invalid request.')],
        ]);
    }

    /**
     * Send SMS to the user.
     */
    protected function sendSms($phoneNumber, $message)
{
    // قم بجلب معلومات الحساب من ملف .env
    $accountSid = env('TWILIO_SID'); // معرف الحساب
    $authToken = env('TWILIO_AUTH_TOKEN'); // رمز التوثيق
    $twilioNumber = env('TWILIO_PHONE_NUMBER'); // رقم Twilio المرسل

    $client = new \Twilio\Rest\Client($accountSid, $authToken);

    try {
        $client->messages->create(
            $phoneNumber= request()->phoneNumber, // رقم المستلم
            [
                'from' => $twilioNumber, // الرقم المرسل
                'body' => $message,     // نص الرسالة
            ]
        );
    } catch (\Exception $e) {
        throw new \Exception(__('Failed to send SMS: ') . $e->getMessage());
    }
}

}
