<?php

namespace App\Services;

use App\Traits\HttpResponses;
use Illuminate\Http\Client\Response as ClientResponse;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class MSG91Service
{
    use HttpResponses;

    const BASE_URL = 'https://api.msg91.com/api/';

    const VERSION = 'v5';

    private $key;

    private $otpTemplate;

    public function __construct()
    {
        $this->key = env('MSG_AUTH_KEY');
        $this->otpTemplate = env('MSG_OTP_TEMPLATE');
    }

    public function sendOtpMessage($countryCode, $mobileNumber): void
    {

        $otp = mt_rand(1000, 9999);

        $url = $this->buildUrl('otp', [
            'authkey' => $this->key,
            'country' => $countryCode,
            'mobile' => $mobileNumber,
            'otp' => $otp,
            'template_id' => $this->otpTemplate,
        ]);
        $response = $this->makeGetRequest($url);

        $this->throwErrorIfFailed($response);
    }

    public function verifyOtp($otp, $countryCode, $mobileNumber)//: void
    {
        $url = $this->buildUrl('otp/verify', [
            'authkey' => $this->key,
            'country' => $countryCode,
            'mobile' => $mobileNumber,
            'otp' => $otp,
        ]);
        $response = $this->makeGetRequest($url);

        $this->throwErrorIfFailed($response, 'Invalid otp!');
    }

    private function buildUrl(string $endPoint, array $params): string
    {
        $query = http_build_query($params);
        $url = self::BASE_URL.self::VERSION."/$endPoint?$query";

        return $url;
    }

    private function makeGetRequest($url): ClientResponse
    {
        $response = Http::withHeader('Content-Type', 'application/json')->get($url);

        return $response;
    }

    public function throwErrorIfFailed($response, $errorMessage = null)
    {
        $response = $response->json();
        if ($response['type'] == 'error') {
            $error = $response['message'];
            $errorMessage = "Msg91 : $error";

            return $this->throwException($errorMessage, Response::HTTP_BAD_REQUEST);
        }
    }
}
