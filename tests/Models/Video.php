<?php

namespace CSlant\LaravelLike\Tests\Models;

use CSlant\LaravelLike\HasLove;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasLove;

    protected $guarded = [];
}
