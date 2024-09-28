<?php

namespace CSlant\LaravelLike;

use CSlant\LaravelLike\Traits\InteractionRelationship;
use CSlant\LaravelLike\Traits\Like\LikeCount;
use CSlant\LaravelLike\Traits\Like\LikeScopes;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait HasLike
 * use this trait in your model to enable liking functionality
 *
 * @package CSlant\LaravelLike
 * @mixin Model
 */
trait HasLike
{
    use InteractionRelationship;
    use LikeScopes;
    use LikeCount;
}
