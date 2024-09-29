<?php

namespace CSlant\LaravelLike\Models;

use CSlant\LaravelLike\Enums\InteractionTypeEnum;
use CSlant\LaravelLike\Traits\InteractionRelationship;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * Class Like
 *
 * @package CSlant\LaravelLike\Models
 * @property int $id
 * @property int $user_id
 * @property int $model_id
 * @property string $model_type
 * @property InteractionTypeEnum $type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Model $user
 * @property Model $model
 *
 * @property-read string $interaction_type getInteractionTypeAttribute() - Get the interaction type attribute. Used for the accessor.
 */
class Like extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'model_id',
        'model_type',
        'type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'model_type' => 'string',
        'type' => InteractionTypeEnum::class,
    ];

    /**
     * Get the user that owns the like.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        $userModel = (string) (config('like.users.model') ?? config('auth.providers.users.model'));
        $userForeignKey = (string) (config('like.users.foreign_key') ?? 'user_id');

        return $this->belongsTo($userModel, $userForeignKey);
    }

    /**
     * Get the model that the like belongs to.
     *
     * @return BelongsTo<Model, self>
     */
    public function model(): BelongsTo
    {
        return $this->morphTo();
    }

    /**
     * Check if the record is liked.
     *
     * @see InteractionRelationship::likeOne()
     *
     * @return bool
     */
    public function isLiked(): bool
    {
        // Use with likeOne() relationship. Can't use with likes() relationship.
        return $this->type->isLike();
    }

    /**
     * Check if the record is disliked.
     *
     * @see InteractionRelationship::likeOne()
     *
     * @return bool
     */
    public function isDisliked(): bool
    {
        // Use with likeOne() relationship. Can't use with likes() relationship.
        return $this->type->isDislike();
    }

    /**
     * Check if the record is loved.
     *
     * @see InteractionRelationship::likeOne()
     *
     * @return bool
     */
    public function isLove(): bool
    {
        // Use with likeOne() relationship. Can't use with likes() relationship.
        return $this->type->isLove();
    }

    /**
     * Scope a query to only include records of a given model type.
     *
     * @param  Builder  $query
     * @param  string  $modelType The model type. E.g. App\Models\Post::class
     *
     * @return Builder
     */
    public function scopeWithModelType(Builder $query, string $modelType): Builder
    {
        // Use with likes() relationship. Can't use with likeOne() relationship.
        return $query->where('model_type', $modelType);
    }

    /**
     * Get the interaction type attribute. Used for the accessor.
     *
     * @return string
     */
    public function getInteractionTypeAttribute(): string
    {
        return $this->type->value;
    }

    /**
     * Toggle the like interaction.
     *
     * @return string
     */
    public function toggleLikeInteraction(): string
    {
        if ($this->isLiked()) {
            $this->type = InteractionTypeEnum::DISLIKE;
        } else {
            $this->type = InteractionTypeEnum::LIKE;
        }

        return $this->type->value;
    }
}
