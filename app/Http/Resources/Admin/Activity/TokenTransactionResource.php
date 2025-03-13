<?php

namespace App\Http\Resources\Admin\Activity;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TokenTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->professor->name,
            'image' => $this->professor->image ?? asset('asset/blank_profile.png'),
            'quarter_name' => $this->professor->room->quarters->name,
            'room_number' => 'No. '.$this->professor->room->name,
            'date' => date('M d, Y', strtotime($this->created_at)),
            'time' => date('h:iA', strtotime($this->created_at)),
            'tokens' => $this->added_coin_count,
        ];
    }
}
