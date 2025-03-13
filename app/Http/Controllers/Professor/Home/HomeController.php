<?php

namespace App\Http\Controllers\Professor\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\Professor\Transaction\TransactionHistoryResource;
use App\Interfaces\ProfessorMilkBookingRepositoryInterface;
use App\Interfaces\ProfessorRepositoryInterface;
use App\Interfaces\ProfessorTransactionRepositoryInterface;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct(
        protected ProfessorRepositoryInterface $professorRepository,
        protected ProfessorTransactionRepositoryInterface $professorTransactionRepository,
        protected ProfessorMilkBookingRepositoryInterface $professorMilkBookingRepository
    ) {}

    public function home(Request $request)
    {
        try {
            $professorToken = $this->jWTService->getUserToken($request->bearerToken());
            $professor = $this->professorRepository->getProfessor($professorToken);
            $availableTokens = $professor->available_coin_count;

            $currentDayRecord = $this->professorTransactionRepository->getCurrentDayRecord($professorToken);
            $date = date('M d, Y');
            $data = $currentDayRecord ? new TransactionHistoryResource($currentDayRecord) : null;
            $data = $data ? $data->toArray($request) : null; // toArray() from Resource class needs A Request class param

            $lastThreaTransactions = $this->professorTransactionRepository->getLastFiveRecords($professorToken);
            $recentRecords = $lastThreaTransactions->count() > 0 ? TransactionHistoryResource::collection($lastThreaTransactions) : [];
            $result = [
                'name' => $professor->name,
                'image' => $professor->image ?? asset('asset/blank_profile.png'),
                'tokens' => $availableTokens,
                'current_day' => [
                    'date' => $date,
                    'spent' => $data['tokens'] ?? '0 Token',
                    'litre' => $data['litre'] ?? '0 Litre',
                ],
                'recent' => $recentRecords,
            ];

            return $this->success('Home screen data', $result);
        } catch (\Exception $e) {
            return $this->error('Something went wrong', $e->getMessage());
        }
    }

    public function bookMilk(Request $request)
    {
        try {
            $professorToken = $this->jWTService->getUserToken($request->bearerToken());
            /**
             * if 6 < curTime < 12 - today eve
             * if 11 < curTime < 20 - next day morning
             */
            $currentDate = date('Y-m-d');
            $currentTime = date('H:i:s');

            $morningStartTime = date('H:i:s', strtotime('11:00:00'));
            $morningEndTime = date('H:i:s', strtotime('20:00:00'));

            $eveningStartTime = date('H:i:s', strtotime('06:00:00'));
            $eveningEndTime = date('H:i:s', strtotime('11:00:00'));

            if ($currentTime > $morningStartTime && $currentTime <= $morningEndTime) { // Next Morning
                $date = date('Y-m-d', strtotime('+1 day'));
                $slot = 'Morning';
            } elseif ($currentTime >= $eveningStartTime && $currentTime <= $eveningEndTime) { // Today Evening
                $date = $currentDate;
                $slot = 'Evening';
            } else {
                return $this->success("You can't book your milk between 8:00 pm & 06:00 am", code: 200, customCode: 400);
            }

            $dateExists = $this->professorMilkBookingRepository->getRecordForProfessor($professorToken, $date, $slot);
            if ($dateExists) {
                $message = $dateExists->delivery_date > $currentDate ?
                    "You have already Booked Milk for Next Day's Morning slot"
                    : "You have already Booked Milk for Today's Evening slot";

                return $this->success($message, code: 200, customCode: 400);
            }
            $data = [
                'professor_token' => $professorToken,
                'time_slot' => $slot,
                'delivery_date' => $date,
                'quantity' => 0.5,
                'status' => 'Pending',
            ];
            $this->professorMilkBookingRepository->create($data);
            $message = $date > $currentDate ?
                'Your milk will be delivered tomorrow morning'
                : 'Your milk will be delivered today evening';

            return $this->success($message, code: 200);
        } catch (\Exception $e) {
            return $this->error('Something went wrong', $e->getMessage());
        }
    }
}
