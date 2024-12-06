<?php

namespace App\Services;

use Twilio\Rest\Client;

class TwilioService
{
    protected $client;
    protected $serviceSid;

    public function __construct()
    {
        $this->client = new Client(
            env('TWILIO_ACCOUNT_SID'),
            env('TWILIO_AUTH_TOKEN')
        );
        $this->serviceSid = env('TWILIO_VERIFY_SERVICE_SID');
    }

    public function sendVerification(string $phoneNumber, string $channel = 'sms')
    {
        try {
            $verification = $this->client->verify->v2
                ->services($this->serviceSid)
                ->verifications
                ->create($phoneNumber, $channel);

            return $verification->sid;
        } catch (\Exception $e) {
            throw new \Exception("Error sending verification: " . $e->getMessage());
        }
    }
}
