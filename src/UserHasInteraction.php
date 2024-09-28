<?php

namespace CSlant\LaravelLike;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Trait UserInteraction
 * use this trait in your User model
 *
 * @package CSlant\LaravelLike\Traits
 * @mixin Model
 * @method HasMany hasMany(string $related, string $foreignKey = null, string $localKey = null)
 */
trait UserHasInteraction
{
    /**
     * Get all likes of the user. This method is used for eager loading.
     *
     * @return HasMany
     */
    public function likes(): HasMany
    {
        $interactionModel = (string) (config('like.interaction_model') ?? 'CSlant\LaravelLike\Models\Like');
        $userForeignKey = (string) (config('like.users.foreign_key') ?? 'user_id');

        return $this->hasMany($interactionModel, $userForeignKey);
    }
}
