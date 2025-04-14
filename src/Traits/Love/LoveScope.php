<?php

namespace CSlant\LaravelLike\Traits\Love;

use CSlant\LaravelLike\Enums\InteractionTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * Trait LoveScope
 *
 * @package CSlant\LaravelLike\Traits\Love
 * @mixin Model
 *
 * @method MorphOne<self, *> likeOne()
 * @method MorphMany<self, *> likes()
 * @method bool isInteractedBy(int $userId, null|InteractionTypeEnum $type)
 */
trait LoveScope
{
    /**
     * The scope locale for select love relationship.
     *
     * @return MorphOne
     */
    public function loveTo(): MorphOne
    {
        return $this->likeOne()->where('type', InteractionTypeEnum::LOVE);
    }

    /**
     * The scope locale for select loves relationship.
     *
     * @return MorphMany
     */
    public function lovesTo(): MorphMany
    {
        return $this->likes()->where('type', InteractionTypeEnum::LOVE);
    }

    /**
     * Check if the model has been loved by the given user.
     *
     * @param  int  $userId
     *
     * @return bool
     */
    public function isLovedBy(int $userId): bool
    {
        return $this->isInteractedBy($userId, InteractionTypeEnum::LOVE);
    }
}
