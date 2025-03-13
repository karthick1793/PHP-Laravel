<?php

namespace App\Traits;

use Illuminate\Support\Facades\Crypt;

trait CryptTrait
{
    public function encryptInputString(string $string)
    {
        return Crypt::encryptString($string);
    }

    public function decryptOrReturnOriginal(string $string)
    {
        try {
            return Crypt::decryptString($string);
        } catch (\Exception $e) {
            return $string;
        }
    }
}
