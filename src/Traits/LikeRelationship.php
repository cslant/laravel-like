<?php

namespace CSlant\LaravelLike\Traits;

use CSlant\LaravelLike\Models\Like;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * Trait LikeRelationship
 *
 * @package CSlant\LaravelLike\Traits
 * @mixin Model
 */
trait LikeRelationship
{
    /**
     * Like has one relationship with the model.
     *
     * @return MorphOne
     */
    public function likeOne(): MorphOne
    {
        return $this->morphOne(Like::class, 'model');
    }

    /**
     * Like has many relationship with the model.
     *
     * @return MorphMany
     */
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'model');
    }
}
