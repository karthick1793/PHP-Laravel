<?php

namespace App\Http\Resources\Admin\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MilkTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $morningTime = $this->morning_time ? date('h:iA', strtotime($this->morning_time)) : '';
        $eveningTime = $this->evening_time ? date('h:iA', strtotime($this->evening_time)) : '';
        $time = ($morningTime && $eveningTime) ? 'Morning & Evening' : ($morningTime ? 'Morning' : 'Evening');

        return [
            'name' => $this->professor->name,
            'quarter_name' => $this->professor->room->quarters->name,
            'room_number' => $this->professor->room->name,
            'date' => date('M d, Y', strtotime($this->created_at)),
            'time' => $time, //date('h:iA', strtotime($this->created_at)),
            'morning_slot' => $this->morning_time ? 'true' : 'false',
            'evening_slot' => $this->evening_time ? 'true' : 'false',
            'litre' => ($this->morning_litre ?? 0) + ($this->evening_litre ?? 0),
        ];
    }
}
