<?php

namespace App\Repositories;

use App\Interfaces\ProfessorMilkBookingRepositoryInterface;
use App\Models\ProfessorMilkBooking;

class ProfessorMilkBookingRepository implements ProfessorMilkBookingRepositoryInterface
{
    public function create($array)
    {
        return ProfessorMilkBooking::create($array);
    }

    public function getById($id)
    {
        return ProfessorMilkBooking::find($id);
    }

    public function getAllBookings($skip = null, $take = null, $date = null, $slot = null, $searchValue = null, $orderBy = 'DESC', $withCount = true, $download = false)
    {
        $data = ProfessorMilkBooking::select([
            'id',
            'professor_token',
            'time_slot',
            'delivery_date',
            'status',
            'quantity',
            'created_at',
        ])
            ->where(function ($query) use ($date, $slot) {
                if ($date) {
                    $query->whereDate('delivery_date', $date);
                }
                if ($slot) {
                    $query->where('time_slot', $slot);
                }
            })
            ->where(function ($query) use ($searchValue) {
                if ($searchValue) {
                    $query->whereRaw("DATE_FORMAT(created_at, '%b %d, %Y') LIKE ?", ["%{$searchValue}%"])
                        ->orWhereRaw("DATE_FORMAT(delivery_date, '%b %d, %Y') LIKE ?", ["%{$searchValue}%"])
                        ->orWhere('time_slot', 'LIKE', "%{$searchValue}%")
                        ->orWhereHas('professor', function ($q) use ($searchValue) {
                            $q->where('name', 'LIKE', "%{$searchValue}%")
                                ->orWhere('mobile_number', 'LIKE', "%{$searchValue}%");
                        })
                        ->orWhereHas('professor.room', function ($q) use ($searchValue) {
                            $q->where('name', 'LIKE', "%{$searchValue}%");
                        })
                        ->orWhereHas('professor.room.quarters', function ($q) use ($searchValue) {
                            $q->where('name', 'LIKE', "%{$searchValue}%");
                        });
                }
            })
            ->with('professor', function ($query) {
                $query->select('token', 'name', 'room_token', 'mobile_number', 'country_code', 'image')
                    ->with('room', function ($q) {
                        $q->select('token', 'quarter_token', 'name')
                            ->with('quarters', function ($q1) {
                                $q1->select('token', 'name');
                            });
                    });
            });

        $count = $withCount ? $data->count() : 0;
        if ($download & $date == date('Y-m-d')) {
            $data = $data->whereNotIn('status', ['Cancelled']);
        }

        if ($skip !== null & $take !== null) {
            $data = $data->skip($skip)->take($take);
        } elseif ($skip !== null & $take === null) {
            $data = $data->skip($skip);
        } elseif ($skip === null & $take !== null) {
            $data = $data->take($take);
        }

        $data = $data->orderBy('id', $orderBy)->get();

        return [
            'data' => $data,
            'count' => $count,
        ];
    }

    public function getProfessorBookings($professorToken, $skip = null, $take = null, $slot = null, $orderBy = 'DESC', $withCount = true)
    {
        $data = ProfessorMilkBooking::select([
            'id',
            'professor_token',
            'time_slot',
            'delivery_date',
            'status',
            'quantity',
            'created_at',
        ])
            ->where(function ($query) use ($slot) {
                if ($slot) {
                    $query->where('time_slot', $slot);
                }
            })
            ->where('professor_token', $professorToken);

        $count = $withCount ? $data->count() : 0;

        if ($skip !== null & $take !== null) {
            $data = $data->skip($skip)->take($take);
        } elseif ($skip !== null & $take === null) {
            $data = $data->skip($skip);
        } elseif ($skip === null & $take !== null) {
            $data = $data->take($take);
        }

        $data = $data->orderBy('id', $orderBy)->get();

        return [
            'data' => $data,
            'count' => $count,
        ];
    }

    public function getBookingsGroupedByDate($skip = null, $take = null, $searchValue = null, $orderBy = 'DESC', $withCount = true)
    {
        $data = ProfessorMilkBooking::select([
            'delivery_date',
            \DB::raw('COUNT(status) as total_count'),
            \DB::raw("COUNT(CASE WHEN status = 'Delivered' THEN 1 END) as delivered_count"),
            \DB::raw("COUNT(CASE WHEN status = 'Pending' THEN 1 END) as pending_count"),
        ])
            ->where(function ($query) use ($searchValue) {
                if ($searchValue) {
                    $query->whereRaw("DATE_FORMAT(delivery_date, '%b %d, %Y') LIKE ?", ["%{$searchValue}%"]);
                    // ->having(function ($query) use ($searchValue) {
                    //     if ($searchValue) {
                    //         $query->orHavingRaw('COUNT(status) LIKE ?', ["%{$searchValue}%"])
                    //             ->orHavingRaw('COUNT(CASE WHEN status = "Delivered" THEN 1 END) LIKE ?', ["%{$searchValue}%"])
                    //             ->orHavingRaw('COUNT(CASE WHEN status = "Pending" THEN 1 END) LIKE ?', ["%{$searchValue}%"]);
                    //     }
                    // });
                }
            });

        $count = $withCount ? $data->groupBy('delivery_date')->count() : 0;
        if ($skip !== null & $take !== null) {
            $data = $data->skip($skip)->take($take);
        } elseif ($skip !== null & $take === null) {
            $data = $data->skip($skip);
        } elseif ($skip === null & $take !== null) {
            $data = $data->take($take);
        }

        $data = $data->orderBy('delivery_date', $orderBy)
            ->groupBy('delivery_date')
            ->get();

        return [
            'data' => $data,
            'count' => $count,
        ];
    }

    public function updateStatus($id, $status)
    {
        ProfessorMilkBooking::where('id', $id)->update([
            'status' => $status,
        ]);
    }

    public function getRecordForProfessor($professorToken, $deliveryDate, $slot)
    {
        return ProfessorMilkBooking::where('professor_token', $professorToken)
            ->where('delivery_date', $deliveryDate)
            ->where('time_slot', $slot)
            ->first();
    }
}
