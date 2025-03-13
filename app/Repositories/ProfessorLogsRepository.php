<?php

namespace App\Repositories;

use App\Interfaces\ProfessorLogsRepositoryInterface;
use App\Models\ProfessorLog;

class ProfessorLogsRepository implements ProfessorLogsRepositoryInterface
{
    public function create($data)
    {
        return ProfessorLog::create($data);
    }

    public function getAllProfessorLogs($skip = null, $take = null, $searchValue = null, $fromDate = null, $toDate = null, $quarterToken = null, $professorToken = null, $orderBy = 'ASC')
    {
        $data = ProfessorLog::select([
            'token',
            'professor_token',
            'old_coin_count',
            'added_coin_count',
            'total_coin_count',
            'created_at',
        ])
            ->where(function ($query) use ($professorToken, $fromDate, $toDate, $quarterToken) {
                if ($professorToken) {
                    $query->where('professor_token', $professorToken);
                }
                if ($fromDate) {
                    $query->whereDate('created_at', '>=', $fromDate);
                }
                if ($toDate) {
                    $query->whereDate('created_at', '<=', $toDate);
                }
                if ($quarterToken) {
                    $query->whereHas('professor.room', function ($q) use ($quarterToken) {
                        $q->where('quarter_token', $quarterToken);
                    });
                }
            })
            ->where(function ($query) use ($searchValue) {
                if ($searchValue) {
                    $query->whereRaw("DATE_FORMAT(created_at, '%b %d, %Y') LIKE ?", ["%{$searchValue}%"])
                        ->orWhereHas('professor', function ($q) use ($searchValue) {
                            $q->where('name', 'LIKE', "%{$searchValue}%")
                                ->orWhere('mobile_number', 'LIKE', "%{$searchValue}%");
                        })
                        ->orWhere('total_coin_count', 'LIKE', "%{$searchValue}%")
                        ->orWhereHas('professor.room', function ($q) use ($searchValue) {
                            $q->where('name', 'LIKE', "%{$searchValue}%");
                        })
                        ->orWhereHas('professor.room.quarters', function ($q) use ($searchValue) {
                            $q->where('name', 'LIKE', "%{$searchValue}%");
                        });
                }
            })
            ->with('professor', function ($query) {
                $query->select('token', 'name', 'room_token')
                    ->with('room', function ($q) {
                        $q->select('token', 'quarter_token', 'name')
                            ->with('quarters', function ($q1) {
                                $q1->select('token', 'name');
                            });
                    });
            });

        if ($skip !== null & $take !== null) {
            $data = $data->skip($skip)
                ->take($take)
                ->orderBy('id', $orderBy)
                ->get();
        } elseif ($skip !== null & $take === null) {
            $data = $data->skip($skip)
                ->orderBy('id', $orderBy)
                ->get();
        } elseif ($skip === null & $take !== null) {
            $data = $data->take($take)
                ->orderBy('id', $orderBy)
                ->get();
        } else {
            $data = $data->orderBy('id', $orderBy)
                ->get();
        }

        return $data;
    }

    public function getAllProfessorLogCount($skip = null, $take = null, $searchValue = null, $fromDate = null, $toDate = null, $quarterToken = null, $professorToken = null)
    {
        return ProfessorLog::select([
            'token',
            'professor_token',
            'old_coin_count',
            'added_coin_count',
            'total_coin_count',
            'created_at',
        ])
            ->where(function ($query) use ($professorToken, $fromDate, $toDate, $quarterToken) {
                if ($professorToken) {
                    $query->where('professor_token', $professorToken);
                }
                if ($fromDate) {
                    $query->whereDate('created_at', '>=', $fromDate);
                }
                if ($toDate) {
                    $query->whereDate('created_at', '<=', $toDate);
                }
                if ($quarterToken) {
                    $query->whereHas('professor.room', function ($q) use ($quarterToken) {
                        $q->where('quarter_token', $quarterToken);
                    });
                }
            })
            ->where(function ($query) use ($searchValue) {
                if ($searchValue) {
                    $query->whereRaw("DATE_FORMAT(created_at, '%b %d, %Y') LIKE ?", ["%{$searchValue}%"])
                        ->orWhereHas('professor', function ($q) use ($searchValue) {
                            $q->where('name', 'LIKE', "%{$searchValue}%")
                                ->orWhere('mobile_number', 'LIKE', "%{$searchValue}%");
                        })
                        ->orWhere('total_coin_count', 'LIKE', "%{$searchValue}%")
                        ->orWhereHas('professor.room', function ($q) use ($searchValue) {
                            $q->where('name', 'LIKE', "%{$searchValue}%");
                        })
                        ->orWhereHas('professor.room.quarters', function ($q) use ($searchValue) {
                            $q->where('name', 'LIKE', "%{$searchValue}%");
                        });
                }
            })
            ->count();
    }
}
