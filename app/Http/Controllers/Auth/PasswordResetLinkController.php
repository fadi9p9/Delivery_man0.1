<?php

namespace App\Http\Controllers\Auth;
use App\Services\TwilioService;

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
    public function store(Request $request, TwilioService $twilioService)
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
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\TestMail($token));

            return response()->json([
                'message' => __('Password reset link sent successfully to email.'),
            ], 200);
        }

        
        if ($request->phoneNumber) {
            // Handle case for phone number reset
            $user = User::where('phoneNumber', $request->phoneNumber)->first();

            if (!$user) {
                throw ValidationException::withMessages([
                    'phoneNumber' => [__('User not found.')],
                ]);
            }

            // Generate reset token
            $token = Password::getRepository()->create($user);

            try {
                // Use TwilioService to send the verification SMS
                $twilioService->sendVerification($user->phoneNumber);

                return response()->json([
                    'message' => __('Password reset token sent successfully via SMS.'),
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => __('Failed to send SMS: ') . $e->getMessage(),
                ], 500);
            }
        }

        throw ValidationException::withMessages([
            'error' => [__('Invalid request.')],
        ]);
    }
}
//         // Handle case for phone number reset
//         if ($request->phoneNumber) {
//             $user = User::where('phoneNumber', $request->phoneNumber)->first();

//             if (!$user) {
//                 throw ValidationException::withMessages([
//                     'phoneNumber' => [__('User not found.')],
//                 ]);
//             }

//             // Generate reset token
//             $token = Password::getRepository()->create($user);

//             // Send SMS
//             $this->sendSms($user->phoneNumber, __('Your reset token is: ') . $token);

//             return response()->json([
//                 'message' => __('Password reset token sent successfully via SMS.'),
//             ], 200);
//         }

//         throw ValidationException::withMessages([
//             'error' => [__('Invalid request.')],
//         ]);
//     }

//    // Method to send SMS
// protected function sendSms($phoneNumber, $message)
// {
//     // Fetch Twilio credentials from the .env file
//     $accountSid = env('TWILIO_ACCOUNT_SID'); // Twilio Account SID
//     $authToken = env('TWILIO_AUTH_TOKEN'); // Twilio Auth Token
//     $twilioNumber = env('TWILIO_PHONE_NUMBER'); // Twilio phone number

//     // Initialize the Twilio client
//     $client = new \Twilio\Rest\Client($accountSid, $authToken);

//     try {
//         // Send the SMS
//         $client->messages->create(
//             $phoneNumber, // Recipient's phone number
//             [
//                 'from' => $twilioNumber, // Twilio phone number
//                 'body' => $message,      // Message body
//             ]
//         );
//     } catch (\Twilio\Exceptions\RestException $e) {
//         // Handle errors from Twilio API
//         throw new \Exception(__('Failed to send SMS: ') . $e->getMessage());
//     } catch (\Exception $e) {
//         // Handle general exceptions
//         throw new \Exception(__('Unexpected error while sending SMS: ') . $e->getMessage());
//     }
// }



