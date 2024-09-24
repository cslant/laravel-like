<?php

namespace CSlant\LaravelLike\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Trait LikeCount
 *
 * @package CSlant\LaravelLike\Traits
 * @mixin Model
 *
 * @method MorphMany likesTo()
 * @method MorphMany dislikesTo()
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
     * Get digital count.
     *
     * @param $count
     *
     * @return string
     */
    public function countDigital($count): string
    {
        if ($count < 1000) {
            return $count;
        }

        if ($count < 1000000) {
            return round($count / 1000, 1) . 'K';
        }

        return round($count / 1000000, 1) . 'M';
    }

    /**
     * Get the count of likes in digital format.
     *
     * @return string
     */
    public function likesCountDigital(): string
    {
        return $this->countDigital($this->likesCount());
    }

    /**
     * Get the count of dislikes in digital format.
     *
     * @return string
     */
    public function dislikesCountDigital(): string
    {
        return $this->countDigital($this->dislikesCount());
    }
}
