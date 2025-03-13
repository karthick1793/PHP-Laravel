<?php

namespace App\Repositories;

use App\Interfaces\ProfessorRepositoryInterface;
use App\Models\Professor;

class ProfessorRepository implements ProfessorRepositoryInterface
{
    public function getAllProfessor()
    {
        return Professor::select('token', 'name', 'image', 'country_code', 'mobile_number', 'available_coin_count')->get();
    }

    public function getAllProfessorWithRelation($skip = null, $take = null, $searchValue = null, $quarterToken = null)
    {
        $data = Professor::select([
            'token',
            'name',
            'country_code',
            'mobile_number',
            'image',
            'available_coin_count',
            'room_token',
        ])
            ->where(function ($query) use ($searchValue) {
                if ($searchValue) {
                    $query->where('name', 'LIKE', "%{$searchValue}%")
                        ->orWhere('mobile_number', 'LIKE', "%{$searchValue}%")
                        ->orWhere('available_coin_count', 'LIKE', "%{$searchValue}%")
                        ->orWhereHas('room', function ($q) use ($searchValue) {
                            $q->where('name', 'LIKE', "%{$searchValue}%");
                        })
                        ->orWhereHas('room.quarters', function ($q) use ($searchValue) {
                            $q->where('name', 'LIKE', "%{$searchValue}%");
                        });
                }
            })
            ->whereHas('room.quarters', function ($query) use ($quarterToken) {
                if ($quarterToken) {
                    $query->where('token', $quarterToken);
                }
            })
            ->with('room', function ($query) {
                $query->select('token', 'quarter_token', 'name')
                    ->with('quarters', function ($q) {
                        $q->select('token', 'name');
                    });
            });
        if ($skip !== null & $take !== null) {
            $data = $data->skip($skip)->take($take)
                ->groupBy('token')
                ->get();
        } elseif ($skip !== null & $take === null) {
            $data = $data->skip($skip)
                ->groupBy('token')
                ->get();
        } elseif ($skip === null & $take !== null) {
            $data = $data->take($take)
                ->groupBy('token')
                ->get();
        } else {
            $data = $data->groupBy('token')->get();
        }

        return $data;
    }

    public function getAllProfessorCount($skip = null, $take = null, $searchValue = null, $quarterToken = null)
    {
        return Professor::select([
            'token',
            'name',
            'country_code',
            'mobile_number',
            'room_token',
            'available_coin_count',
        ])
            ->where(function ($query) use ($searchValue) {
                if ($searchValue) {
                    $query->where('name', 'LIKE', "%{$searchValue}%")
                        ->orWhere('mobile_number', 'LIKE', "%{$searchValue}%")
                        ->orWhere('available_coin_count', 'LIKE', "%{$searchValue}%")
                        ->orWhereHas('room', function ($q) use ($searchValue) {
                            $q->where('name', 'LIKE', "%{$searchValue}%");
                        })
                        ->orWhereHas('room.quarters', function ($q) use ($searchValue) {
                            $q->where('name', 'LIKE', "%{$searchValue}%");
                        });
                }
            })
            ->whereHas('room.quarters', function ($query) use ($quarterToken) {
                if ($quarterToken) {
                    $query->where('token', $quarterToken);
                }
            })
            ->groupBy('token')
            ->count();
    }

    public function getProfessor($professorToken)
    {
        return Professor::where('token', $professorToken)->first();
    }

    public function checkIfProfessorExists(string $countryCode, string $mobileNumber): bool
    {
        return Professor::where('country_code', $countryCode)->where('mobile_number', $mobileNumber)->exists();

    }

    public function professorMobileUpdateOTPExpiry(string $mobile): bool
    {

        $expiryOtpDate = date('Y-m-d H:i:s', strtotime('+3 minutes'));

        return Professor::where('mobile_number', $mobile)
            ->update([
                'otp_sms_valid_time' => $expiryOtpDate,
            ]);
    }

    public function getProfessorWithMobile(string $countryCode, string $mobileNumber): ?Professor
    {
        return Professor::where('country_code', $countryCode)
            ->where('mobile_number', $mobileNumber)
            ->first();
    }

    public function decrementTokenIfAvailable($professorToken): bool
    {
        $professor = $this->getProfessor($professorToken);
        if ($professor->available_coin_count > 1) {
            $professor->decrement('available_coin_count');

            return true;
        } else {
            return false;
        }
    }

    public function incrementToken($professorToken, $tokens)
    {
        Professor::where('token', $professorToken)->increment('available_coin_count', $tokens);
    }
}
