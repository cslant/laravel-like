<?php

namespace CSlant\LaravelLike\Tests\Models;

use CSlant\LaravelLike\HasLike;
use CSlant\LaravelLike\HasLove;
use Illuminate\Database\Eloquent\Model;

class Podcast extends Model
{
    use HasLike;
    use HasLove;

    protected $guarded = [];
}
