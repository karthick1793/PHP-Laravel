<?php

namespace App\Services;

use App\Interfaces\ProfessorMilkBookingRepositoryInterface;
use App\Interfaces\ProfessorRepositoryInterface;
use App\Interfaces\ProfessorTransactionRepositoryInterface;
use App\Traits\HttpResponses;

class MilkBookingService
{
    use HttpResponses;

    public function __construct(
        public ProfessorMilkBookingRepositoryInterface $professorMilkBookingRepository,
        public ProfessorTransactionRepositoryInterface $professorTransactionRepository,
        public ProfessorRepositoryInterface $professorRepository
    ) {}

    public function updateAllBookingsOfTheDay($date, $slot, $status)
    {
        $records = $this->professorMilkBookingRepository->getAllBookings(date: $date, slot: $slot);
        $bookingIds = $records['data']->pluck('id');

        $this->updateGivenBookingsOfTheDay($bookingIds, $date, $slot, $status);
    }

    public function updateGivenBookingsOfTheDay($bookingIds, $date, $slot, $status)
    {
        $currentDate = date('Y-m-d');
        //Validate future date
        if ($date > $currentDate) {
            $this->throwException("Tomorrow's bookings can't be updated!");
        }
        foreach ($bookingIds as $id) {
            $bookingData = $this->professorMilkBookingRepository->getById($id);
            $professorToken = $bookingData->professor_token;
            $professor = $this->professorRepository->getProfessor($professorToken);
            $professorName = $professor->name;
            $dataStatus = $bookingData->status;
            $deliveryDate = $bookingData->delivery_date;
            if ($deliveryDate != $date) {
                $this->throwException('Delivery Date & Record date does not match!');
            }
            if ($dataStatus == 'Delivered') {
                continue;
                // $this->throwException("Milk has been delivered for $professorName");
            }
            if ($dataStatus == 'Cancelled') {
                continue;
                // $this->throwException("This booking has been cancelled by $professorName!");
            }
            if ($status == $dataStatus) {
                continue;
                // $this->throwException("Status has already been updated for $professorName");
            }
            if ($status == 'Delivered') {
                $slot = $bookingData->time_slot;
                $createdAt = date('Y-m-d H:i:s', strtotime($deliveryDate));
                //Already updated
                $currentDayRecord = $this->professorTransactionRepository->getGivenDayRecord($professorToken, $deliveryDate);
                if ($currentDayRecord) {
                    if (($slot == 'Morning' && $currentDayRecord->morning_time) || ($slot == 'Evening' && $currentDayRecord->evening_time)) {
                        continue;
                        // $this->throwException('Milk has already been delivered for this record');
                    }
                }
                //Not enough token
                $decreaseToken = $this->professorRepository->decrementTokenIfAvailable($professorToken);
                if (! $decreaseToken) {
                    $this->throwException("Professor $professorName doesn't have enough token!");
                }

                $currentTime = date('H:i:s');
                if ($slot == 'Morning') {
                    $data = [
                        'professor_token' => $professorToken,
                        'morning_time' => $currentTime,
                        'morning_litre' => 0.5,
                        'created_at' => $createdAt,
                    ];
                } else {
                    $data = [
                        'professor_token' => $professorToken,
                        'evening_time' => $currentTime,
                        'evening_litre' => 0.5,
                        'created_at' => $createdAt,
                    ];
                }

                $this->professorTransactionRepository->createOrUpdate($professorToken, $data, $deliveryDate);
                $bookingData->update(['status' => $status]);
            } else {
                $bookingData->update(['status' => $status]);
            }
        }
    }
}
