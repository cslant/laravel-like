<?php

namespace CSlant\LaravelLike\Tests\FakeModels;

use CSlant\LaravelLike\UserHasInteraction;
use Illuminate\Database\Eloquent\Model;

class FakeUser extends Model
{
    use UserHasInteraction;

    protected $table = 'users';
    protected $guarded = [];
}
