<?php

namespace CSlant\LaravelLike;

use CSlant\LaravelLike\Traits\Love\LoveScope;
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
    use LoveScope;

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
