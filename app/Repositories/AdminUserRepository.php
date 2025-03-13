<?php

namespace App\Repositories;

use App\Interfaces\AdminUserRepositoryInterface;
use App\Models\AdminUser;

class AdminUserRepository implements AdminUserRepositoryInterface
{
    public function create(array $admin)
    {
        return AdminUser::create($admin);
    }

    public function getAdminWithEmail(string $email)
    {
        return AdminUser::where('email', $email)->first();
    }

    public function updateOTP(string $employeeEmail, $otp, $expiryTime): int
    {
        return AdminUser::where('email', $employeeEmail)->update([
            'otp' => $otp,
            'otp_valid_time' => $expiryTime,
        ]);
    }

    public function updatePassword(string $email, string $password)
    {
        return AdminUser::where('email', $email)->update(['password' => $password]);

    }
}
