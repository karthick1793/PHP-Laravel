<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Contracts\JWTSubject;

class AdminUser extends Authenticatable implements JWTSubject
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'token',
        'email',
        'name',
        'password',
        'image',
        'otp',
        'otp_valid_time',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            $model->token = Str::ulid();
        });
    }

    protected $hidden = [
        'password',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey(); // typically the user's primary key

    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
