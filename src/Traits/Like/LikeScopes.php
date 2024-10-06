<?php

namespace CSlant\LaravelLike\Traits\Like;

use CSlant\LaravelLike\Enums\InteractionTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * Trait LikeScopes
 *
 * @package CSlant\LaravelLike\Traits\Like
 * @mixin Model
 *
 * @method MorphOne likeOne()
 * @method MorphMany likes()
 */
trait LikeScopes
{
    /**
     * The scope locale for select like relationship with the model (one).
     *
     * @return MorphOne
     */
    public function likeTo(): MorphOne
    {
        return $this->likeOne()->where('type', InteractionTypeEnum::LIKE);
    }

    /**
     * The scope locale for select dislike relationship with the model (one).
     *
     * @return MorphOne
     */
    public function dislikeTo(): MorphOne
    {
        return $this->likeOne()->where('type', InteractionTypeEnum::DISLIKE);
    }

    /**
     * The scope locale for select likes relationship (all).
     *
     * @return MorphMany
     */
    public function likesTo(): MorphMany
    {
        return $this->likes()->where('type', InteractionTypeEnum::LIKE);
    }

    /**
     * The scope locale for select dislikes relationship (all).
     *
     * @return MorphMany
     */
    public function dislikesTo(): MorphMany
    {
        return $this->likes()->where('type', InteractionTypeEnum::DISLIKE);
    }

    /**
     * Check if the model has been interacted by the given user.
     *
     * @param  int  $userId
     *
     * @return bool
     */
    public function isLikedBy(int $userId): bool
    {
        return $this->isInteractedBy($userId, InteractionTypeEnum::LIKE);
    }

    /**
     * Check if the model has been interacted by the given user.
     *
     * @param  int  $userId
     *
     * @return bool
     */
    public function isDislikedBy(int $userId): bool
    {
        return $this->isInteractedBy($userId, InteractionTypeEnum::DISLIKE);
    }
}
