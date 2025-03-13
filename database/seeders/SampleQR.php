<?php

namespace Database\Seeders;

use App\Models\QrCode;
use App\Traits\CryptTrait;
use Illuminate\Database\Seeder;

class SampleQR extends Seeder
{
    use CryptTrait;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        QrCode::create([
            'name' => $this->encryptInputString('TEST_MILK_VENDING'),
        ]);
    }
}
