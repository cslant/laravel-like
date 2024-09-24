<?php

namespace CSlant\LaravelLike;

use CSlant\LaravelLike\Traits\LikeCount;
use CSlant\LaravelLike\Traits\LikeRelationship;
use CSlant\LaravelLike\Traits\LikeScopes;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait HasLike
 *
 * @package CSlant\LaravelLike
 * @mixin Model
 */
trait HasLike
{
    use LikeRelationship, LikeScopes {
        LikeRelationship::likes insteadof LikeScopes;
        LikeRelationship::likeOne insteadof LikeScopes;
        LikeScopes::likes as likesScope;
        LikeScopes::likeOne as likeOneScope;
    }
    use LikeCount;
}
