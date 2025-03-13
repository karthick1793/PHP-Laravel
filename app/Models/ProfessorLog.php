<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProfessorLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'token',
        'professor_token',
        'old_coin_count',
        'added_coin_count',
        'total_coin_count',
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
