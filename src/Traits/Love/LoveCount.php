<?php

namespace CSlant\LaravelLike\Traits\Love;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Trait LoveScopes
 *
 * @package CSlant\LaravelLike\Traits\Love
 * @mixin Model
 *
 * @method MorphMany<self, Model> lovesTo()
 */
trait LoveCount
{
    /**
     * Get the count of loves.
     *
     * @return int
     */
    public function lovesCount(): int
    {
        return $this->lovesTo()->count();
    }

    /**
     * Get the count of loves in digital format.
     *
     * @return string
     */
    public function lovesCountDigital(): string
    {
        return count_digital($this->lovesCount());
    }
}
