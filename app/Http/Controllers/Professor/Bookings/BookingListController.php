<?php

namespace App\Http\Controllers\Professor\Bookings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Professor\Bookings\CancelBookingRequest;
use App\Interfaces\ProfessorMilkBookingRepositoryInterface;
use Illuminate\Http\Request;

class BookingListController extends Controller
{
    public function __construct(
        protected ProfessorMilkBookingRepositoryInterface $professorMilkBookingRepository
    ) {}

    public function bookingList(Request $request)
    {
        try {
            $professorToken = $this->jWTService->getUserToken($request->bearerToken());

            $rawData = $this->professorMilkBookingRepository->getProfessorBookings($professorToken);
            $data = $rawData['data']->map(function ($value) {
                $slot = $value['time_slot'];
                $createdDate = date('d/m/Y', strtotime($value['created_at']));
                $createdTime = date('h:i A', strtotime($value['created_at']));
                $deliveryDate = date('d M Y', strtotime($value['delivery_date']));
                $bool = $this->canCancelBooking($deliveryDate, $slot);

                return [
                    'id' => $value['id'],
                    'date' => $createdDate,
                    'delivery_date' => $deliveryDate,
                    'time' => $createdTime,
                    'slot' => $slot,
                    'status' => $value['status'],
                    'can_cancel' => $bool,
                ];
            });
            $totalCount = $rawData['count'];
            $result = [
                'count' => $totalCount,
                'data' => $data,
            ];

            return $this->success('Booked list', $result);
        } catch (\Exception $e) {
            return $this->error('Something went wrong', $e->getMessage());
        }
    }

    public function cancelBooking(CancelBookingRequest $request)
    {
        try {
            $id = $request->id;

            $data = $this->professorMilkBookingRepository->getById($id);
            if ($data->status == 'Cancelled') {
                return $this->success('Booking has already been cancelled', code: 200, customCode: 400);
            }

            if ($this->canCancelBooking($data->delivery_date, $data->time_slot)) {
                $data->update(['status' => 'Cancelled']);

                return $this->success('Bookings has been cancelled');
            } else {
                return $this->success("Bookings can't be cancelled", code: 200, customCode: 400);
            }
        } catch (\Exception $e) {
            return $this->error('Something went wrong', $e->getMessage());
        }
    }

    public function canCancelBooking($deliveryDate, $slot): bool
    {
        $currentDate = date('d-m-Y');
        $currentTime = date('H:i:s');

        $deliveryDate = date('d-m-Y', strtotime($deliveryDate));
        $bool = true;
        if ($deliveryDate < $currentDate) {
            $bool = false;
        } elseif ($deliveryDate == $currentDate && $slot == 'Evening' && $currentTime > date('H:i:s', strtotime('11:00:00'))) {
            $bool = false;
        } elseif ($deliveryDate == $currentDate && $slot == 'Morning') {
            $bool = false;
        }

        return $bool;
    }
}
