<?php

namespace CSlant\LaravelLike\Traits;

use CSlant\LaravelLike\Contracts\LikeManager as LikeManagerContract;
use CSlant\LaravelLike\Enums\InteractionTypeEnum;
use CSlant\LaravelLike\LikeManager;
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
 * @method MorphOne<self, *> morphOne(string $related, string $name, string $type = null, string $id = null, string $localKey = null)
 * @method MorphMany<self, *> morphMany(string $related, string $name, string $type = null, string $id = null, string $localKey = null)
 */
trait InteractionRelationship
{
    use ForgetsInteractions;

    /**
     * Interaction has one relationship with the model.
     *
     * @return MorphOne
     */
    public function likeOne(): MorphOne
    {
        return $this->morphOne((string) (config('like.interaction_model') ?? Like::class), 'model');
    }

    /**
     * Interaction has many relationship with the model.
     *
     * @return MorphMany
     */
    public function likes(): MorphMany
    {
        return $this->morphMany((string) (config('like.interaction_model') ?? Like::class), 'model');
    }

    /**
     * Like the model for the current user.
     *
     * @param  null|int  $userId
     *
     * @return Like
     */
    public function like(?int $userId = null): Like
    {
        return $this->likeManager()->like($this, $userId);
    }

    /**
     * Dislike the model for the current user.
     *
     * @param  null|int  $userId
     *
     * @return Like
     */
    public function dislike(?int $userId = null): Like
    {
        return $this->likeManager()->dislike($this, $userId);
    }

    /**
     * Love the model for the current user.
     *
     * @param  null|int  $userId
     *
     * @return Like
     */
    public function love(?int $userId = null): Like
    {
        return $this->likeManager()->love($this, $userId);
    }

    /**
     * Remove the current user's like.
     *
     * @param  null|int  $userId
     *
     * @return bool
     */
    public function unlike(?int $userId = null): bool
    {
        return $this->likeManager()->unlike($this, $userId);
    }

    /**
     * Remove the current user's love.
     *
     * @param  null|int  $userId
     *
     * @return bool
     */
    public function unlove(?int $userId = null): bool
    {
        return $this->likeManager()->unlove($this, $userId);
    }

    /**
     * Remove the current user's dislike.
     *
     * @param  null|int  $userId
     *
     * @return bool
     */
    public function unDislike(?int $userId = null): bool
    {
        return $this->likeManager()->unDislike($this, $userId);
    }

    /**
     * Toggle the current user's interaction.
     *
     * @param  null|int  $userId
     *
     * @return null|Like
     */
    public function toggle(?int $userId = null): ?Like
    {
        return $this->likeManager()->toggle($this, $userId);
    }

    /**
     * Check if the current user has liked the model.
     *
     * @param  null|int  $userId
     *
     * @return bool
     */
    public function isLiked(?int $userId = null): bool
    {
        return $this->likeManager()->isLiked($this, $userId);
    }

    /**
     * Check if the current user has disliked the model.
     *
     * @param  null|int  $userId
     *
     * @return bool
     */
    public function isDisliked(?int $userId = null): bool
    {
        return $this->likeManager()->isDisliked($this, $userId);
    }

    /**
     * Check if the current user has loved the model.
     *
     * @param  null|int  $userId
     *
     * @return bool
     */
    public function isLoved(?int $userId = null): bool
    {
        return $this->likeManager()->isLoved($this, $userId);
    }

    /**
     * Resolve the like manager from the container.
     *
     * @return LikeManager
     */
    public function likeManager(): LikeManager
    {
        /** @var LikeManager $manager */
        $manager = app(LikeManagerContract::class);

        return $manager;
    }

    /**
     * Get the interaction of the given user.
     *
     * @param  int  $userId
     * @param  null|InteractionTypeEnum  $interactionType
     *
     * @return MorphMany
     */
    public function withInteractionBy(int $userId, ?InteractionTypeEnum $interactionType = null): MorphMany
    {
        $userForeignKey = (string) (config('like.users.foreign_key') ?? 'user_id');

        $query = $this->likes()->where($userForeignKey, $userId);

        if ($interactionType && InteractionTypeEnum::isValid($interactionType)) {
            $query->where('type', $interactionType);
        }

        return $query;
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
        return $this->withInteractionBy($userId, $interactionType)->exists();
    }
}
