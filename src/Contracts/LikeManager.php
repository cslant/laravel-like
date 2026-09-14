<?php

namespace CSlant\LaravelLike\Contracts;

use CSlant\LaravelLike\Models\Like;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface LikeManager
{
    public function like(Model $model, ?int $userId = null): Like;

    public function dislike(Model $model, ?int $userId = null): Like;

    public function love(Model $model, ?int $userId = null): Like;

    public function unlike(Model $model, ?int $userId = null): bool;

    public function unlove(Model $model, ?int $userId = null): bool;

    public function unDislike(Model $model, ?int $userId = null): bool;

    public function toggle(Model $model, ?int $userId = null): ?Like;

    public function isLiked(Model $model, ?int $userId = null): bool;

    public function isDisliked(Model $model, ?int $userId = null): bool;

    public function isLoved(Model $model, ?int $userId = null): bool;

    public function likesCount(Model $model): int;

    public function dislikesCount(Model $model): int;

    public function lovesCount(Model $model): int;

    public function totalCount(Model $model): int;

    public function userInteractions(?int $userId = null): Collection;

    public function userLikedModels(?int $userId = null): Collection;
}
