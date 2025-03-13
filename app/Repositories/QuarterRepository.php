<?php

namespace App\Repositories;

use App\Interfaces\QuarterRepositoryInterface;
use App\Models\Quarter;

class QuarterRepository implements QuarterRepositoryInterface
{
    public function getAllQuatersWithRooms()
    {
        return Quarter::select('token', 'name')
            ->with('rooms', function ($q) {
                $q->select('token', 'quarter_token', 'name');
            })
            ->get();
    }
}
