<?php

namespace CSlant\LaravelLike\Facades;

use CSlant\LaravelLike\Contracts\LikeManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \CSlant\LaravelLike\Models\Like like(\Illuminate\Database\Eloquent\Model $model, ?int $userId = null)
 * @method static \CSlant\LaravelLike\Models\Like dislike(\Illuminate\Database\Eloquent\Model $model, ?int $userId = null)
 * @method static \CSlant\LaravelLike\Models\Like love(\Illuminate\Database\Eloquent\Model $model, ?int $userId = null)
 * @method static bool unlike(\Illuminate\Database\Eloquent\Model $model, ?int $userId = null)
 * @method static bool unlove(\Illuminate\Database\Eloquent\Model $model, ?int $userId = null)
 * @method static bool unDislike(\Illuminate\Database\Eloquent\Model $model, ?int $userId = null)
 * @method static \CSlant\LaravelLike\Models\Like|null toggle(\Illuminate\Database\Eloquent\Model $model, ?int $userId = null)
 * @method static bool isLiked(\Illuminate\Database\Eloquent\Model $model, ?int $userId = null)
 * @method static bool isDisliked(\Illuminate\Database\Eloquent\Model $model, ?int $userId = null)
 * @method static bool isLoved(\Illuminate\Database\Eloquent\Model $model, ?int $userId = null)
 * @method static int likesCount(\Illuminate\Database\Eloquent\Model $model)
 * @method static int dislikesCount(\Illuminate\Database\Eloquent\Model $model)
 * @method static int lovesCount(\Illuminate\Database\Eloquent\Model $model)
 * @method static int totalCount(\Illuminate\Database\Eloquent\Model $model)
 * @method static \Illuminate\Support\Collection userInteractions(?int $userId = null)
 * @method static \Illuminate\Support\Collection userLikedModels(?int $userId = null)
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
