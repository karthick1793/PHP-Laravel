<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ProfessorTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'token',
        'professor_token',
        'morning_time',
        'morning_litre',
        'evening_time',
        'evening_litre',
        'created_at',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            $model->token = Str::ulid();
        });
    }

    public function professor()
    {
        return $this->belongsTo(Professor::class, 'professor_token', 'token');
    }
}
