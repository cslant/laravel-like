<?php

namespace CSlant\LaravelLike;

use CSlant\LaravelLike\Traits\InteractionRelationship;
use CSlant\LaravelLike\Traits\Love\LoveCount;
use CSlant\LaravelLike\Traits\Love\LoveScope;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait HasLove
 * use this trait in your model(post, article,...) to enable liking functionality
 *
 * @package CSlant\LaravelLike
 * @mixin Model
 */
trait HasLove
{
    use InteractionRelationship;
    use LoveCount;
    use LoveScope;
}
