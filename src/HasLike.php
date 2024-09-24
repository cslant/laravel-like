<?php

namespace CSlant\LaravelLike;

use CSlant\LaravelLike\Enums\LikeTypeEnum;
use CSlant\LaravelLike\Models\Like;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * Trait HasLike
 *
 * @package CSlant\LaravelLike
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait HasLike
{
    /**
     * Like has one relationship with the model.
     *
     * @return MorphOne
     */
    public function likeOne(): MorphOne
    {
        return $this->morphOne(Like::class, 'model')->where('type', LikeTypeEnum::LIKE);
    }

    /**
     * Like has many relationship with the model for likes.
     *
     * @return MorphMany
     */
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'model')->where('type', LikeTypeEnum::LIKE);
    }

    /**
     * Like has one relationship with the model for dislikes.
     *
     * @return MorphOne
     */
    public function dislikeOne(): MorphOne
    {
        return $this->morphOne(Like::class, 'model')->where('type', LikeTypeEnum::DISLIKE);
    }

    /**
     * Like has many relationship with the model for dislikes.
     *
     * @return MorphMany
     */
    public function dislikes(): MorphMany
    {
        return $this->morphMany(Like::class, 'model')->where('type', LikeTypeEnum::DISLIKE);
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
