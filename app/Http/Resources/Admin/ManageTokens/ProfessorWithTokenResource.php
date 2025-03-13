<?php

namespace App\Http\Resources\Admin\ManageTokens;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfessorWithTokenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'token' => $this->token,
            'name' => $this->name,
            'image' => $this->image ?? asset('asset/blank_profile.png'),
            'number' => '(+'.$this->country_code.') '.$this->mobile_number,
            'quarters_name' => $this->room->quarters->name,
            'room_number' => 'No. '.$this->room->name,
            'tokens' => $this->available_coin_count,
        ];
    }
}
