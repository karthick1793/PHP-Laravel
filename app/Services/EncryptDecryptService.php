<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class EncryptDecryptService
{
    private static $AES_SECRET_KEY;

    private static $AES_ALGORITHM = 'AES-128-ECB';

    public static function setAesImageSecretKey($key)
    {
        self::$AES_SECRET_KEY = $key;
    }

    public static function encryptOTP($OTP)
    {
        if (empty($OTP)) {
            return null;
        }
        try {
            if (strlen(self::$AES_SECRET_KEY) !== 32) {
                throw new \Exception('Invalid AES key length. The key should be 32 characters (16 bytes).');
            }
            $encrypted = openssl_encrypt(
                $OTP,
                self::$AES_ALGORITHM,
                hex2bin(self::$AES_SECRET_KEY),
                OPENSSL_RAW_DATA
            );

            // Return Base64 encoded encrypted data
            return base64_encode($encrypted);
        } catch (\Exception $e) {
            Log::error('Encryption error: '.$e->getMessage());

            return null;
        }
    }

    public static function decryptOTP($encryptOTP)
    {
        if (empty($encryptOTP)) {
            return 'null';
        }
        try {

            $decoded = base64_decode($encryptOTP);
            $decrypted = openssl_decrypt(
                $decoded,
                self::$AES_ALGORITHM,
                hex2bin(self::$AES_SECRET_KEY),
                OPENSSL_RAW_DATA
            );

            return $decrypted;
        } catch (\Exception $e) {
            Log::error('Decryption error: '.$e->getMessage());

            return null;
        }
    }
}
