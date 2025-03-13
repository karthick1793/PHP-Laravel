<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfessorMilkBooking extends Model
{
    use HasFactory;

    protected $table = 'professor_milk_booking';

    protected $fillable = [
        'professor_token',
        'time_slot',
        'delivery_date',
        'quantity',
        'status',
    ];

    public function professor()
    {
        return $this->belongsTo(Professor::class, 'professor_token', 'token');
    }
}
