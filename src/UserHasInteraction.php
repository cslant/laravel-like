<?php

namespace CSlant\LaravelLike;

use CSlant\LaravelLike\Enums\InteractionTypeEnum;
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

    /**
     * Check if the user has liked the given model.
     *
     * @param  string  $interactionType
     *
     * @return static
     */
    public function forgetInteractionsOfType(string $interactionType): static
    {
        $this->likes()->where('type', $interactionType)->delete();

        return $this;
    }

    /**
     * Check if the user has liked the given model.
     *
     * @param  null|string  $interactionType
     *
     * @return static
     */
    public function forgetInteractions(?string $interactionType = null): static
    {
        if ($interactionType && in_array($interactionType, InteractionTypeEnum::getValuesAsStrings())) {
            return $this->forgetInteractionsOfType($interactionType);
        }

        $this->likes()->delete();

        return $this;
    }
}
