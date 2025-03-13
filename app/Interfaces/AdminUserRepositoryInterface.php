<?php

namespace App\Interfaces;

interface AdminUserRepositoryInterface
{
    public function getAdminWithEmail(string $email);

    public function create(array $admin);

    public function updatePassword(string $employeeEmail, string $password);

    public function updateOTP(string $employeeEmail, $otp, $expiryTime): int;
}
