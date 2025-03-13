<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Quarter extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'token',
        'name',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            $model->token = Str::ulid();
        });
    }

    public function rooms()
    {
        return $this->hasMany(QuarterRoom::class, 'quarter_token', 'token');
    }
}
