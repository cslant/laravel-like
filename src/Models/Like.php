<?php

namespace CSlant\LaravelLike\Models;

use CSlant\LaravelLike\Enums\LikeTypeEnum;
use CSlant\LaravelLike\Traits\InteractionRelationship;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class Like
 *
 * @package CSlant\LaravelLike\Models
 * @property int $id
 * @property int $user_id
 * @property int $model_id
 * @property string $model_type
 * @property LikeTypeEnum $type
 * @property Carbon $created_at
 * @property Carbon $updated_at
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
        'type' => LikeTypeEnum::class,
    ];

    /**
     * Check if the record is liked.
     * Use with likeOne() relationship. Can't use with likes() relationship.
     *
     * @see InteractionRelationship::likeOne()
     *
     * @return bool
     */
    public function isLiked(): bool
    {
        return $this->type->isLike();
    }

    /**
     * Check if the record is disliked.
     * Use with likeOne() relationship. Can't use with likes() relationship.
     *
     * @see InteractionRelationship::likeOne()
     *
     * @return bool
     */
    public function isDisliked(): bool
    {
        return $this->type->isDislike();
    }

    /**
     * Check if the record is loved.
     * Use with likeOne() relationship. Can't use with likes() relationship.
     *
     * @see InteractionRelationship::likeOne()
     *
     * @return bool
     */
    public function isLove(): bool
    {
        return $this->type->isLove();
    }
}
