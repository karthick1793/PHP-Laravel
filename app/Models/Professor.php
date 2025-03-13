<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Professor extends Authenticatable implements JWTSubject
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'token',
        'name',
        'country_code',
        'mobile_number',
        'image',
        'available_coin_count',
        'room_token',
        'otp',
        'otp_sms_valid_time',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            $model->token = Str::ulid();
        });
    }

    public function getJWTIdentifier()
    {
        return $this->getKey(); // typically the user's primary key

    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function room()
    {
        return $this->belongsTo(QuarterRoom::class, 'room_token', 'token');
    }
}
