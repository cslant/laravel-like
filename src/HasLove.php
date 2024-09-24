<?php

namespace CSlant\LaravelLike;

use CSlant\LaravelLike\Enums\LikeTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * Trait HasLove
 *
 * @package CSlant\LaravelLike
 * @mixin Model
 *
 * @method MorphOne likeOne()
 * @method MorphMany likes()
 */
trait HasLove
{
    /**
     * The scope locale for select love relationship.
     *
     * @return MorphOne
     */
    public function loveTo(): MorphOne
    {
        return $this->likeOne()->where('type', LikeTypeEnum::LOVE);
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

    /**
     * Get the count of loves.
     *
     * @return int
     */
    public function lovesCount(): int
    {
        return $this->lovesTo()->count();
    }
}
