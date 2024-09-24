<?php

namespace CSlant\LaravelLike\Traits;

use CSlant\LaravelLike\Enums\LikeTypeEnum;
use CSlant\LaravelLike\Models\Like;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * Trait LikeScopes
 *
 * @package CSlant\LaravelLike\Traits
 * @mixin Model
 */
trait LikeScopes
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

    /**
     * The scope locale for select like relationship.
     *
     * @return MorphOne
     */
    public function likeTo(): MorphOne
    {
        return $this->likeOne()->where('type', LikeTypeEnum::LIKE);
    }

    /**
     * The scope locale for select dislike relationship.
     *
     * @return MorphOne
     */
    public function dislikeTo(): MorphOne
    {
        return $this->likeOne()->where('type', LikeTypeEnum::DISLIKE);
    }

    /**
     * The scope locale for select likes relationship.
     *
     * @return MorphMany
     */
    public function likesTo(): MorphMany
    {
        return $this->likes()->where('type', LikeTypeEnum::LIKE);
    }

    /**
     * The scope locale for select dislikes relationship.
     *
     * @return MorphMany
     */
    public function dislikesTo(): MorphMany
    {
        return $this->likes()->where('type', LikeTypeEnum::DISLIKE);
    }

    /**
     * The scope locale for select loves relationship.
     *
     * @return MorphMany
     */
    public function lovesTo(): MorphMany
    {
        return $this->likes()->where('type', LikeTypeEnum::LOVE);
    }
}
