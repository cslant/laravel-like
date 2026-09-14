<?php

namespace CSlant\LaravelLike;

use CSlant\LaravelLike\Models\Like;
use CSlant\LaravelLike\Traits\ForgetsInteractions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Trait UserInteraction
 * use this trait in your User model
 *
 * @package CSlant\LaravelLike\Traits
 * @mixin Model
 * @method HasMany<Like, Model> hasMany(string $related, string $foreignKey = null, string $localKey = null)
 */
trait UserHasInteraction
{
    use ForgetsInteractions;

    /**
     * Get all likes of the user. This method is used for eager loading.
     *
     * @return HasMany<self, Model>
     */
    public function likes(): HasMany
    {
        $interactionModel = (string) (config('like.interaction_model') ?? Like::class);
        $userForeignKey = (string) (config('like.users.foreign_key') ?? 'user_id');

        return $this->hasMany($interactionModel, $userForeignKey);
    }
}
