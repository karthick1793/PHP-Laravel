<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\ChangePasswordRequest;
use App\Http\Requests\Admin\Auth\CreateAdminRequest;
use App\Http\Requests\Admin\Auth\ForgotPasswordRequest;
use App\Http\Requests\Admin\Auth\LoginRequest;
use App\Http\Requests\Admin\Auth\VerifyMailOtpRequest;
use App\Interfaces\AdminUserRepositoryInterface;
use App\Mail\SendEmailOtp;
use App\Services\EncryptDecryptService;
use App\Services\MSG91Service;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function __construct(
        protected AdminUserRepositoryInterface $adminRepository,
        protected EncryptDecryptService $encryptDecryptService,
        protected MSG91Service $msg91Service,
    ) {}

    public function create(CreateAdminRequest $request)
    {
        try {
            $email = $request->email;
            $name = $request->name;
            $password = bcrypt($request->password);

            $adminExists = $this->adminRepository->getAdminWithEmail($email);
            if ($adminExists) {
                return $this->error('Admin has already been created under this email!');
            }
            $this->adminRepository->create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
            ]);

            return $this->success('Admin has been created');
        } catch (\Exception $e) {
            return $this->error('Something went wrong', $e->getMessage());
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $credentials = $request->only('email', 'password');
            if (! \Auth::guard('admin')->attempt($credentials)) {
                return $this->error('Incorrect email/password', [], 401);
            }

            $token = $this->jWTService->accessToken('admin', $credentials);
            $user = \Auth::guard('admin')->user();

            return $this->success('Login successfully', [
                'name' => $user->name,
                'email' => $user->email,
                'image' => $user->image ?? asset('asset/blank_profile.png'),
                'token' => $token,
            ]);
        } catch (\Exception $e) {
            return $this->error('Something went wrong', $e->getMessage());
        }
    }

    public function sendOtp(ForgotPasswordRequest $request)
    {
        try {
            $email = $request->email;
            $otp = rand(1000, 9999);
            $expiryMinute = 5;
            $expiryTime = date('Y-m-d H:i:s', strtotime("+$expiryMinute minutes"));

            $encryptOTP = $this->encryptDecryptService->encryptOTP($otp);
            $getAdmin = $this->adminRepository->getAdminWithEmail($email);
            $name = $getAdmin->name;
            $this->adminRepository->updateOTP($email, $encryptOTP, $expiryTime);
            Mail::to($email)->send(new SendEmailOtp($email, $otp, $name, $expiryMinute));

            return $this->success('Otp has been sent to your email id');
        } catch (\Exception $e) {
            return $this->error('something Went wrong', $e->getMessage());
        }
    }

    public function verifyOtp(VerifyMailOtpRequest $request)
    {
        try {
            $email = $request->email;
            $otp = $request->otp;
            $this->encryptDecryptService->setAesImageSecretKey(env('AES_SECRET_KEY'));

            $admin = $this->adminRepository->getAdminWithEmail($email);
            $encryptyOTP = $admin->otp;
            //check time
            $expiryOTPTime = $admin->resend_otp_status == 0 ? $admin->otp_valid_time : $admin->resend_otp_date_time;
            $currentTime = date('Y-m-d H:i:s');
            if ($currentTime > $expiryOTPTime) {
                return $this->error('Your OTP has expired, please try again');
            }

            $decryptedOTP = $this->encryptDecryptService->decryptOTP($encryptyOTP);
            if ($otp != $decryptedOTP) {
                return $this->error('Otp does not match');
            }
            $this->adminRepository->updateOTP($email, null, null);
            $data = [
                'name' => $admin->name,
                'email' => $admin->email,
            ];

            return $this->success('success', $data);
        } catch (\Exception $e) {
            return $this->error('Something went wrong');
        }
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $email = $request->email;
            $password = bcrypt($request->password);

            $this->adminRepository->updatePassword($email, $password);

            return $this->success('Password has been changed successfully');
        } catch (\Exception $e) {
            return $this->error('Something went wrong', $e->getMessage());
        }
    }

    public function logout()
    {
        $this->jWTService->invalidateToken();

        return $this->success('Successfully logged out.');
    }
}
