<?php

namespace App\Http\Resources\Professor\Transaction;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $currentDay = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $givenDate = date('Y-m-d', strtotime($this->created_at));
        $date = $givenDate == $currentDay ? 'Today' : ($givenDate == $yesterday ? 'Yesterday' : date('M d, Y', strtotime($givenDate)));
        $morningTime = $this->morning_time ? date('h:iA', strtotime($this->morning_time)) : '';
        $eveningTime = $this->evening_time ? date('h:iA', strtotime($this->evening_time)) : '';
        // $time = ($morningTime && $eveningTime) ? "$morningTime & $eveningTime" : ($morningTime ? $morningTime : $eveningTime);
        $time = ($morningTime && $eveningTime) ? 'Morning & Evening' : ($morningTime ? 'Morning' : 'Evening');
        $tokens = ($this->morning_time ? 1 : 0) + ($this->evening_time ? 1 : 0);
        $litres = ($this->morning_litre ?? 0) + ($this->evening_litre ?? 0);

        return [
            'date' => $date,
            'time' => $time,
            'tokens' => $tokens > 1 ? "$tokens Tokens" : "$tokens Token",
            'litre' => $litres.' Litre',
        ];
    }
}
