<?php

namespace CSlant\LaravelLike;

use CSlant\LaravelLike\Traits\Like\LikeCount;
use CSlant\LaravelLike\Traits\Like\LikeScopes;
use CSlant\LaravelLike\Traits\LikeRelationship;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait HasLike
 *
 * @package CSlant\LaravelLike
 * @mixin Model
 */
trait HasLike
{
    use LikeRelationship;
    use LikeScopes;
    use LikeCount;
}
