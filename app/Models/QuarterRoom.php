<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class QuarterRoom extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'token',
        'quarter_token',
        'name',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            $model->token = Str::ulid();
        });
    }

    public function quarters()
    {
        return $this->belongsTo(Quarter::class, 'quarter_token', 'token');
    }
}
