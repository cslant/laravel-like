<?php

namespace CSlant\LaravelLike;

use CSlant\LaravelLike\Traits\InteractionRelationship;
use CSlant\LaravelLike\Traits\Love\LoveCount;
use CSlant\LaravelLike\Traits\Love\LoveScope;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait HasLove
 *
 * @package CSlant\LaravelLike
 * @mixin Model
 */
trait HasLove
{
    use InteractionRelationship;
    use LoveScope;
    use LoveCount;
}
