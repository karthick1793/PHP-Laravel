<?php

namespace App\Http\Controllers\Professor\Scanner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Professor\Scanner\BuyMilkRequest;
use App\Interfaces\ProfessorRepositoryInterface;
use App\Interfaces\ProfessorTransactionRepositoryInterface;
use App\Interfaces\QrCodeRepositoryInterface;
use Illuminate\Http\Request;

class ScannerController extends Controller
{
    public function __construct(
        protected QrCodeRepositoryInterface $qrCodeRepository,
        protected ProfessorRepositoryInterface $professorRepository,
        protected ProfessorTransactionRepositoryInterface $professorTransactionRepository,
    ) {}

    public function buyMilk(Request $request) // BuyMilkRequest
    {
        try {
            $value = $request->value;

            $qrValue = $this->qrCodeRepository->getQrData()->name;
            $decryptedValue = $this->decryptOrReturnOriginal($qrValue);
            if ($value != $decryptedValue) {
                return $this->success('Invalid QR Code', code: 200, customCode: 422);
            }

            \DB::beginTransaction();
            $professorToken = $this->jWTService->getUserToken($request->bearerToken());
            $decreaseToken = $this->professorRepository->decrementTokenIfAvailable($professorToken);
            if (! $decreaseToken) {
                return $this->success("You don't have enough token!", code: 200, customCode: 451);
            }

            $currentTime = date('H:i:s');
            $morningTime = date('H:i:s', strtotime('11:59:59 am'));
            $slot = $currentTime > $morningTime ? 'evening' : 'morning';
            $currentDayRecord = $this->professorTransactionRepository->getCurrentDayRecord($professorToken);
            if ($currentDayRecord) {
                if (($slot == 'morning' && $currentDayRecord->morning_time) || ($slot == 'evening' && $currentDayRecord->evening_time)) {
                    return $this->success("You have already bought your $slot milk!", code: 200, customCode: 451);
                }
            }

            if ($slot == 'morning') {
                $data = [
                    'professor_token' => $professorToken,
                    'morning_time' => $currentTime,
                    'morning_litre' => 0.5,
                ];
            } else {
                $data = [
                    'professor_token' => $professorToken,
                    'evening_time' => $currentTime,
                    'evening_litre' => 0.5,
                ];
            }
            $this->professorTransactionRepository->createOrUpdate($professorToken, $data);
            \DB::commit();

            return $this->success('Milk has been delivered successfully');
        } catch (\Exception $e) {
            return $this->error('Something went wrong', $e->getMessage());
        }
    }
}
