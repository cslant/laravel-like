<?php

namespace CSlant\LaravelLike\Facades;

use CSlant\LaravelLike\Contracts\LikeManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \CSlant\LaravelLike\Models\Like love(\Illuminate\Database\Eloquent\Model $model, ?int $userId = null)
 * @method static bool unlove(\Illuminate\Database\Eloquent\Model $model, ?int $userId = null)
 * @method static bool isLoved(\Illuminate\Database\Eloquent\Model $model, ?int $userId = null)
 * @method static int lovesCount(\Illuminate\Database\Eloquent\Model $model)
 * @method static int totalCount(\Illuminate\Database\Eloquent\Model $model)
 * @method static \Illuminate\Support\Collection<int, \CSlant\LaravelLike\Models\Like> userInteractions(?int $userId = null)
 * @method static array<int|string, int> likeCountsFor(\Illuminate\Support\Collection<int, \Illuminate\Database\Eloquent\Model> $models, \CSlant\LaravelLike\Enums\InteractionTypeEnum $type)
 * @method static \Illuminate\Support\Collection<string, \CSlant\LaravelLike\Models\Like> userInteractionsFor(\Illuminate\Support\Collection<int, \Illuminate\Database\Eloquent\Model> $models, ?int $userId = null)
 */
class Love extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return LikeManager::class;
    }
}
