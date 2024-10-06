<?php

namespace CSlant\LaravelLike\Traits;

use CSlant\LaravelLike\Enums\InteractionTypeEnum;
use CSlant\LaravelLike\Models\Like;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * Trait InteractionRelationship
 *
 * @package CSlant\LaravelLike\Traits
 * @mixin Model
 *
 * @method MorphOne morphOne(string $related, string $name, string $type = null, string $id = null, string $localKey = null)
 * @method MorphMany morphMany(string $related, string $name, string $type = null, string $id = null, string $localKey = null)
 */
trait InteractionRelationship
{
    /**
     * Interaction has one relationship with the model.
     *
     * @return MorphOne
     */
    public function likeOne(): MorphOne
    {
        return $this->morphOne((string) config('like.interaction_model') ?? Like::class, 'model');
    }

    /**
     * Interaction has many relationship with the model.
     *
     * @return MorphMany
     */
    public function likes(): MorphMany
    {
        return $this->morphMany((string) config('like.interaction_model') ?? Like::class, 'model');
    }

    /**
     * Check if the model has been interacted by the given user.
     *
     * @param  int  $userId
     * @param  null|InteractionTypeEnum  $interactionType
     *
     * @return bool
     */
    public function isInteractedBy(int $userId, ?InteractionTypeEnum $interactionType = null): bool
    {
        $userForeignKey = (string) (config('like.users.foreign_key') ?? 'user_id');

        $query = $this->likes()->where($userForeignKey, $userId);

        if (in_array($interactionType, InteractionTypeEnum::getValuesAsStrings())) {
            $query->where('type', $interactionType);
        }

        return $query->exists();
    }

    /**
     * Check and forget all recorded interactions of the given type.
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
     * Check and forget all recorded interactions.
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
