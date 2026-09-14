<?php

namespace CSlant\LaravelLike\Tests\Models;

use CSlant\LaravelLike\HasLike;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasLike;

    protected $guarded = [];
}
