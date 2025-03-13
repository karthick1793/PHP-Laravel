<?php

namespace App\Http\Controllers\Admin\Delivery;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Delivery\DownloadDataOfDateRequest;
use App\Http\Requests\Admin\Delivery\ListCountByDateRequest;
use App\Http\Requests\Admin\Delivery\ListDataOfDateRequest;
use App\Http\Requests\Admin\Delivery\UpdateBookingRequest;
use App\Http\Requests\Admin\Delivery\UpdateInBulkRequest;
use App\Interfaces\ProfessorMilkBookingRepositoryInterface;
use App\Interfaces\ProfessorRepositoryInterface;
use App\Interfaces\ProfessorTransactionRepositoryInterface;
use App\Services\FileDownloadService;
use App\Services\MilkBookingService;

class DeliveryController extends Controller
{
    public function __construct(
        protected ProfessorMilkBookingRepositoryInterface $professorMilkBookingRepository,
        protected FileDownloadService $fileDownloadService,
        protected ProfessorRepositoryInterface $professorRepository,
        protected ProfessorTransactionRepositoryInterface $professorTransactionRepository,
        protected MilkBookingService $milkBookingService
    ) {}

    public function listDeliveryCountsByDate(ListCountByDateRequest $request)
    {
        try {
            $page = $request->page;
            $perPage = $request->per_page;
            $searchValue = $request->search_value;

            $skip = ($page - 1) * $perPage;
            $take = $perPage;

            $rawData = $this->professorMilkBookingRepository->getBookingsGroupedByDate($skip, $take, $searchValue);
            $data = $rawData['data']->map(function ($value) {
                $date = $value['delivery_date'];
                $value['delivery_date'] = date('M d, Y', strtotime($date));
                $value['date'] = $date;

                return $value;
            });
            $totalCount = $rawData['count'];
            $displayCount = $data->count();

            $result = [
                'page' => $page,
                'iTotalRecords' => $totalCount,
                'iTotalDisplayRecords' => $displayCount,
                'aaData' => $data,
            ];

            return $this->success('Booking list by date', $result);
        } catch (\Exception $e) {
            return $this->error('Something went wrong', $e->getMessage());
        }
    }

    public function listDeliveryDataOfDate(ListDataOfDateRequest $request)
    {
        try {
            $page = $request->page;
            $perPage = $request->per_page;
            $searchValue = $request->search_value;
            $date = $request->date;
            $slot = $request->slot;

            $skip = ($page - 1) * $perPage;
            $take = $perPage;

            $rawData = $this->professorMilkBookingRepository->getAllBookings($skip, $take, $date, $slot, $searchValue);
            $data = $rawData['data']->map(function ($value) {
                $data = [
                    'id' => $value['id'],
                    'quarter_name' => $value['professor']['room']['quarters']['name'],
                    'room_number' => $value['professor']['room']['name'],
                    'name' => $value['professor']['name'],
                    'image' => $value['professor']['image'] ?? asset('asset/blank_profile.png'),
                    'mobile_number' => '(+'.$value['professor']['country_code'].') '.$value['professor']['mobile_number'],
                    'slot' => $value['time_slot'],
                    'booked_date' => date('M d, Y', strtotime($value['created_at'])),
                    'delivery_date' => date('M d, Y', strtotime($value['delivery_date'])),
                    'quantity' => $value['quantity'].' Litre',
                    'status' => $value['status'],
                ];

                return $data;
            });
            $totalCount = $rawData['count'];
            $displayCount = $data->count();

            $result = [
                'page' => $page,
                'iTotalRecords' => $totalCount,
                'iTotalDisplayRecords' => $displayCount,
                'aaData' => $data,
            ];

            return $this->success('Booking list by date', $result);
        } catch (\Exception $e) {
            return $this->error('Something went wrong', $e->getMessage());
        }
    }

    public function downloadDeliveryDataOfDate(DownloadDataOfDateRequest $request)
    {
        try {
            $fileType = $request->type;
            $date = $request->date;
            $slot = $request->slot;

            $rawData = $this->professorMilkBookingRepository->getAllBookings(date: $date, slot: $slot, download: true);
            $slNo = 0;
            $data = $rawData['data']->map(function ($value) use (&$slNo) {
                $item = [
                    'sl_no' => ++$slNo,
                    'name' => $value['professor']['name'],
                    'quarter_name' => $value['professor']['room']['quarters']['name'],
                    'room_number' => $value['professor']['room']['name'],
                    'mobile_number' => $value['professor']['mobile_number'], // '(+'.$value['professor']['country_code']. ') '.
                    'slot' => $value['time_slot'],
                    'booked_date' => date('M d, Y', strtotime($value['created_at'])),
                    'delivery_date' => date('M d, Y', strtotime($value['delivery_date'])),
                    'quantity' => $value['quantity'].' Litre',
                    'status' => $value['status'],
                ];

                return $item;
            })->toArray();

            $fileName = 'Bookings';
            $columns = ['Sl No', 'Name', 'Quarters Name', 'Room Number', 'Mobile Number', 'Time Slot', 'Booked Date', 'Delivery Date', 'Quantity', 'Status'];
            if ($fileType == 'csv') {
                return $this->fileDownloadService->generateCsv($data, $columns, $fileName);
            } elseif ($fileType == 'xlsx') {
                return $this->fileDownloadService->generateXlsx($data, $columns, $fileName);
            } elseif ($fileType == 'pdf') {
                $viewFile = 'pdf.admin.delivery.bookings';

                return $this->fileDownloadService->generatePdf($data, $viewFile, $fileName);
            }
        } catch (\Exception $e) {
            return $this->error('Something went wrong', $e->getMessage());
        }
    }

    public function updateBookingStatus(UpdateBookingRequest $request)
    {
        try {
            $id = $request->id;
            $status = $request->status;

            \DB::beginTransaction();
            $bookingData = $this->professorMilkBookingRepository->getById($id);
            $slot = $bookingData->time_slot;
            $dataStatus = $bookingData->status;
            $currentDate = date('Y-m-d');
            $deliveryDate = $bookingData->delivery_date;
            if ($dataStatus == 'Delivered') {
                return $this->error('Milk has been delivered for this record');
            }
            if ($dataStatus == 'Cancelled') {
                return $this->error('This booking has been cancelled by the professor!');
            }
            if ($status == $dataStatus) {
                return $this->error('Status has already been updated for this record');
            }
            if ($deliveryDate == $currentDate) {
                $currentTime = date('H:i:s');
                $morningTime = date('H:i:s', strtotime('7:30:00'));
                $eveningTime = date('H:i:s', strtotime('16:00:00'));
                if ($slot == 'Morning' && $currentTime < $morningTime) {
                    return $this->error('Morning delivery updates are available after 7:30 PM. Please check back later to update the status.');
                } elseif ($slot == 'Evening' && $currentTime < $eveningTime) {
                    return $this->error('Evening delivery updates are available after 4:00 PM. Please check back later to update the status.');
                }
            }

            //Validate future date
            if ($deliveryDate > $currentDate) {
                return $this->error("Tomorrow's bookings can't be updated!");
            }
            if ($status == 'Delivered') {
                $professorToken = $bookingData->professor_token;
                $createdAt = date('Y-m-d H:i:s', strtotime($deliveryDate));
                //Already updated
                $currentDayRecord = $this->professorTransactionRepository->getGivenDayRecord($professorToken, $deliveryDate);
                if ($currentDayRecord) {
                    if (($slot == 'Morning' && $currentDayRecord->morning_time) || ($slot == 'Evening' && $currentDayRecord->evening_time)) {
                        return $this->error('Milk has already been delivered for this record');
                    }
                }
                //Not enough token
                $decreaseToken = $this->professorRepository->decrementTokenIfAvailable($professorToken);
                if (! $decreaseToken) {
                    return $this->error("Professor don't have enough token!");
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
            \DB::commit();

            return $this->success('Booking status has been updated');
        } catch (\Exception $e) {
            return $this->error('Something went wrong', $e->getMessage());
        }
    }

    public function bulkBookingUpdate(UpdateInBulkRequest $request)
    {
        try {
            $type = $request->type; // all, multi
            $slot = $request->slot;
            $status = $request->status;
            $date = $request->date;
            $bookingIds = $request->booking_ids;

            \DB::beginTransaction();
            if ($type == 'all') {
                $this->milkBookingService->updateAllBookingsOfTheDay($date, $slot, $status);
            } else {
                $this->milkBookingService->updateGivenBookingsOfTheDay($bookingIds, $date, $slot, $status);
            }
            \DB::commit();

            return $this->success('Bookings have been update');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
