<?php

namespace CSlant\LaravelLike\Models;

use CSlant\LaravelLike\Enums\LikeTypeEnum;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $fillable = [
        'user_id',
        'model_id',
        'model_type',
        'type',
    ];

    protected $casts = [
        'model_type' => 'string',
        'type' => LikeTypeEnum::class,
    ];
}
