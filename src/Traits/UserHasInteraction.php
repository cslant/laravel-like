<?php

namespace CSlant\LaravelLike\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Trait UserInteraction
 * use this trait in your User model
 *
 * @package CSlant\LaravelLike\Traits
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait UserHasInteraction
{
    /**
     * @return HasMany
     */
    public function like(): HasMany
    {
        $interactionModel = (string) (config('like.interaction_model') ?? 'CSlant\LaravelLike\Models\Like');
        $userForeignKey = (string) (config('like.users.foreign_key') ?? 'user_id');

        return $this->hasMany($interactionModel, $userForeignKey);
    }
}
