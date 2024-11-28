<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramController extends Controller
{
    public function sendVerificationCode(Request $request)
    {
        $request->validate([
            'telegram_chat_id' => 'required',
            'verification_code' => 'required',
        ]);

        $chatId = $request->input('telegram_chat_id');
        $verificationCode = $request->input('verification_code');

        try {
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => "رمز التحقق الخاص بك هو: $verificationCode",
            ]);

            return response()->json(['message' => 'تم إرسال رمز التحقق بنجاح.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'فشل إرسال رمز التحقق.', 'details' => $e->getMessage()], 500);
        }
    }
}
