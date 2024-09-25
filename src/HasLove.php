<?php

namespace CSlant\LaravelLike;

use CSlant\LaravelLike\Traits\InteractionRelationship;
use CSlant\LaravelLike\Traits\Love\LoveCount;
use CSlant\LaravelLike\Traits\Love\LoveScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * Trait HasLove
 *
 * @package CSlant\LaravelLike
 * @mixin Model
 *
 * @method MorphOne likeOne()
 * @method MorphMany likes()
 */
trait HasLove
{
    use InteractionRelationship;
    use LoveScope;
    use LoveCount;
}
