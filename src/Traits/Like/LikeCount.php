<?php

namespace CSlant\LaravelLike\Traits\Like;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Trait LikeCount
 *
 * @package CSlant\LaravelLike\Traits\Like
 * @mixin Model
 *
 * @method MorphMany<self, *> likesTo()
 * @method MorphMany<self, *> dislikesTo()
 */
trait LikeCount
{
    /**
     * Get the count of likes.
     *
     * @return int
     */
    public function likesCount(): int
    {
        return $this->likesTo()->count();
    }

    /**
     * Get the count of dislikes.
     *
     * @return int
     */
    public function dislikesCount(): int
    {
        return $this->dislikesTo()->count();
    }

    /**
     * Get the count of likes in digital format.
     *
     * @return string
     */
    public function likesCountDigital(): string
    {
        return count_digital($this->likesCount());
    }

    /**
     * Get the count of dislikes in digital format.
     *
     * @return string
     */
    public function dislikesCountDigital(): string
    {
        return count_digital($this->dislikesCount());
    }
}
