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
 * @method MorphOne likeOne()
 * @method MorphMany likes()
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
}
