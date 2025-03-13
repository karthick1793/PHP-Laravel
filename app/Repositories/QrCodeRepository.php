<?php

namespace App\Repositories;

use App\Interfaces\QrCodeRepositoryInterface;
use App\Models\QrCode;

class QrCodeRepository implements QrCodeRepositoryInterface
{
    public function getQrData()
    {
        return QrCode::first();
    }
}
