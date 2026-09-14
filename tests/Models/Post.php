<?php

namespace CSlant\LaravelLike\Tests\Models;

use CSlant\LaravelLike\HasLike;
use CSlant\LaravelLike\HasLove;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasLike;
    use HasLove;

    protected $guarded = [];
}
