<?php

namespace App\Repositories;

use App\Interfaces\ProfessorTransactionRepositoryInterface;
use App\Models\ProfessorTransaction;

class ProfessorTransactionRepository implements ProfessorTransactionRepositoryInterface
{
    public function getAllProfessorTransaction($skip = null, $take = null, $searchValue = null, $fromDate = null, $toDate = null, $quarterToken = null, $professorToken = null, $orderBy = 'ASC')
    {
        $data = ProfessorTransaction::select([
            'token',
            'professor_token',
            'morning_time',
            'morning_litre',
            'evening_time',
            'evening_litre',
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
                        ->orWhereRaw('IFNULL(morning_litre,0) + IFNULL(evening_litre,0) LIKE ?', ["%{$searchValue}%"])
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

    public function getAllProfessorTransactionCount($skip = null, $take = null, $searchValue = null, $fromDate = null, $toDate = null, $quarterToken = null, $professorToken = null, $orderBy = 'ASC')
    {
        return ProfessorTransaction::select([
            'token',
            'professor_token',
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
                        ->orWhereRaw('IFNULL(morning_litre,0) + IFNULL(evening_litre,0) LIKE ?', ["%{$searchValue}%"])
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

    public function getProfessorTransaction($professorToken, $skip = null, $take = null)
    {
        $data = ProfessorTransaction::where('professor_token', $professorToken);
        if ($skip !== null & $take !== null) {
            $data = $data->skip($skip)->take($take);
        } elseif ($skip !== null & $take === null) {
            $data = $data->skip($skip);
        } elseif ($skip === null & $take !== null) {
            $data = $data->take($take);
        }

        return $data->orderBy('created_at', 'DESC')->get();
    }

    public function createOrUpdate($professorToken, $data, $date)
    {
        $curretDayRecordExist = $this->getGivenDayRecord($professorToken, $date);
        if ($curretDayRecordExist) {
            $curretDayRecordExist->update($data);
        } else {
            ProfessorTransaction::create($data);
        }
    }

    public function getGivenDayRecord($professorToken, $date)
    {
        return ProfessorTransaction::where('professor_token', $professorToken)
            ->whereDate('created_at', $date)
            ->first();
    }

    public function getCurrentDayRecord($professorToken)
    {
        return ProfessorTransaction::where('professor_token', $professorToken)
            ->whereDate('created_at', now())
            ->first();
    }

    public function getLastFiveRecords($professorToken)
    {
        return ProfessorTransaction::where('professor_token', $professorToken)
            ->orderBy('created_at', 'DESC')
            ->take(5)
            ->get();
    }
}
