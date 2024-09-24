<?php

namespace CSlant\LaravelLike\Traits;

use Illuminate\Database\Eloquent\Model;

/**
 * Trait LikeCount
 *
 * @package CSlant\LaravelLike\Traits
 * @mixin Model
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
     * Get the count of loves.
     *
     * @return int
     */
    public function lovesCount(): int
    {
        return $this->lovesTo()->count();
    }
}
