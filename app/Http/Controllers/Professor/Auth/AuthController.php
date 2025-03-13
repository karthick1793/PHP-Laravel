<?php

namespace App\Http\Controllers\Professor\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Professor\Auth\SendOtpRequest;
use App\Http\Requests\Professor\Auth\VerifyOtpRequest;
use App\Interfaces\ProfessorRepositoryInterface;
use App\Services\MSG91Service;

class AuthController extends Controller
{
    public function __construct(
        protected ProfessorRepositoryInterface $professorRepository,
        protected MSG91Service $mSG91Service,
    ) {}

    public function sendOtp(SendOtpRequest $request)
    {
        try {
            $countryCode = 91; //$request->country_code;
            $mobileNumber = $request->mobile_number;
            $professorExists = $this->professorRepository->checkIfProfessorExists($countryCode, $mobileNumber);
            if (! $professorExists) {
                return $this->error('Account not found!', code: 401);
            }
            $this->mSG91Service->sendOtpMessage($countryCode, $mobileNumber);
            $this->professorRepository->professorMobileUpdateOTPExpiry($mobileNumber);

            return $this->success('Otp has been sent to your registered mobile number!');

        } catch (\Exception $e) {
            return $this->throwException($e->getMessage());
        }
    }

    public function verifyOtp(VerifyOtpRequest $request)
    {
        try {
            $countryCode = 91; //$request->country_code;
            $mobileNumber = $request->mobile_number;
            $otp = $request->otp;
            $credentials['country_code'] = $countryCode;
            $credentials['mobile_number'] = $mobileNumber;

            $professor = $this->professorRepository->getProfessorWithMobile($countryCode, $mobileNumber);
            $expiryOTPTime = $professor->otp_sms_valid_time;
            $currentTime = date('Y-m-d H:i:s');
            if ($currentTime > $expiryOTPTime) {
                return $this->error('Your OTP has expired, please try again.');
            }
            $this->mSG91Service->verifyOtp($otp, $countryCode, $mobileNumber);

            $accessToken = $this->jWTService->accessToken('professor', $credentials);
            if (! $accessToken) {
                return $this->error('Account not found!', code: 401);
            }

            $data = [
                'token' => $accessToken,
                'name' => $professor->name,
                'country_code' => $professor->country_code,
                'mobile_number' => $professor->mobile_number,
                'available_coin_count' => $professor->available_coin_count,
            ];

            return $this->success('Logged in successfully', $data);
        } catch (\Exception $e) {
            return $this->throwException($e->getMessage());
        }
    }

    public function logout()
    {
        try {
            $this->jWTService->invalidateToken();

            return $this->success('Successfully logged out.', [], 200);
        } catch (\Exception $e) {
            return $this->throwException($e->getMessage());
        }
    }
}
