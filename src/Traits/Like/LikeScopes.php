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
     * Implement change like interaction. (like/dislike)
     *
     * @return string
     */
    public function toggleLikeInteraction(): string
    {
        if ($this->likeOne->isLiked()) {
            $this->likeOne->type = InteractionTypeEnum::DISLIKE;
        } else {
            $this->likeOne->type = InteractionTypeEnum::LIKE;
        }

        return $this->likeOne->type->value;
    }
}
