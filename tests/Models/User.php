<?php

namespace CSlant\LaravelLike\Tests\Models;

use CSlant\LaravelLike\UserHasInteraction;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;

class User extends Model implements AuthenticatableContract
{
    use Authenticatable;
    use UserHasInteraction;

    protected $guarded = [];
}
