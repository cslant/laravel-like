<?php

namespace CSlant\LaravelLike\Facades;

use CSlant\LaravelLike\Contracts\LikeManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \CSlant\LaravelLike\Models\Like like(\Illuminate\Database\Eloquent\Model $model, ?int $userId = null)
 * @method static \CSlant\LaravelLike\Models\Like dislike(\Illuminate\Database\Eloquent\Model $model, ?int $userId = null)
 * @method static bool unlike(\Illuminate\Database\Eloquent\Model $model, ?int $userId = null)
 * @method static bool unDislike(\Illuminate\Database\Eloquent\Model $model, ?int $userId = null)
 * @method static \CSlant\LaravelLike\Models\Like|null toggle(\Illuminate\Database\Eloquent\Model $model, ?int $userId = null)
 * @method static bool isLiked(\Illuminate\Database\Eloquent\Model $model, ?int $userId = null)
 * @method static bool isDisliked(\Illuminate\Database\Eloquent\Model $model, ?int $userId = null)
 * @method static int likesCount(\Illuminate\Database\Eloquent\Model $model)
 * @method static int dislikesCount(\Illuminate\Database\Eloquent\Model $model)
 * @method static int totalCount(\Illuminate\Database\Eloquent\Model $model)
 * @method static \Illuminate\Support\Collection<int, \CSlant\LaravelLike\Models\Like> userInteractions(?int $userId = null)
 * @method static \Illuminate\Support\Collection<int, \Illuminate\Database\Eloquent\Model> userLikedModels(?int $userId = null)
 * @method static array<int|string, int> likeCountsFor(\Illuminate\Support\Collection<int, \Illuminate\Database\Eloquent\Model> $models, \CSlant\LaravelLike\Enums\InteractionTypeEnum $type = \CSlant\LaravelLike\Enums\InteractionTypeEnum::LIKE)
 * @method static \Illuminate\Support\Collection<string, \CSlant\LaravelLike\Models\Like> userInteractionsFor(\Illuminate\Support\Collection<int, \Illuminate\Database\Eloquent\Model> $models, ?int $userId = null)
 */
class Like extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return LikeManager::class;
    }
}
