<?php

namespace CSlant\LaravelLike;

use CSlant\LaravelLike\Contracts\LikeManager as LikeManagerContract;
use CSlant\LaravelLike\Enums\InteractionTypeEnum;
use CSlant\LaravelLike\Models\Like;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class LikeManager implements LikeManagerContract
{
    public function like(Model $model, ?int $userId = null): Like
    {
        return $this->setInteraction($model, InteractionTypeEnum::LIKE, $userId);
    }

    public function dislike(Model $model, ?int $userId = null): Like
    {
        return $this->setInteraction($model, InteractionTypeEnum::DISLIKE, $userId);
    }

    public function love(Model $model, ?int $userId = null): Like
    {
        return $this->setInteraction($model, InteractionTypeEnum::LOVE, $userId);
    }

    public function unlike(Model $model, ?int $userId = null): bool
    {
        return $this->removeInteraction($model, InteractionTypeEnum::LIKE, $userId);
    }

    public function unlove(Model $model, ?int $userId = null): bool
    {
        return $this->removeInteraction($model, InteractionTypeEnum::LOVE, $userId);
    }

    public function unDislike(Model $model, ?int $userId = null): bool
    {
        return $this->removeInteraction($model, InteractionTypeEnum::DISLIKE, $userId);
    }

    public function toggle(Model $model, ?int $userId = null): ?Like
    {
        $this->assertModel($model);
        $userId = $this->resolveUserId($userId);
        $current = $this->findInteraction($model, $userId);

        if ($current === null) {
            $created = $this->createInteraction($model, $userId, InteractionTypeEnum::LIKE);
            $this->forgetCounts($model);

            return $created;
        }

        if ($current->type->isDislike()) {
            $updated = $this->updateType($current, InteractionTypeEnum::LIKE);
            $this->forgetCounts($model);

            return $updated;
        }

        if ($current->type->isLike()) {
            $removed = $this->removeRecord($current);
            $this->forgetCounts($model);

            return $removed ? null : $current;
        }

        return $current;
    }

    public function isLiked(Model $model, ?int $userId = null): bool
    {
        return $this->hasInteraction($model, InteractionTypeEnum::LIKE, $userId);
    }

    public function isDisliked(Model $model, ?int $userId = null): bool
    {
        return $this->hasInteraction($model, InteractionTypeEnum::DISLIKE, $userId);
    }

    public function isLoved(Model $model, ?int $userId = null): bool
    {
        return $this->hasInteraction($model, InteractionTypeEnum::LOVE, $userId);
    }

    public function likesCount(Model $model): int
    {
        return $this->countForType($model, InteractionTypeEnum::LIKE);
    }

    public function dislikesCount(Model $model): int
    {
        return $this->countForType($model, InteractionTypeEnum::DISLIKE);
    }

    public function lovesCount(Model $model): int
    {
        return $this->countForType($model, InteractionTypeEnum::LOVE);
    }

    public function totalCount(Model $model): int
    {
        $this->assertModel($model);

        return $this->newInteractionQuery()
            ->where('model_id', $model->getKey())
            ->where('model_type', $model->getMorphClass())
            ->count();
    }

    /**
     * @return Collection<int, Like>
     * @throws AuthenticationException
     */
    public function userInteractions(?int $userId = null): Collection
    {
        $userId = $this->resolveUserId($userId);

        return $this->newInteractionQuery()
            ->where($this->userForeignKey(), $userId)
            ->get();
    }

    /** @return Collection<int, Model> */
    public function userLikedModels(?int $userId = null): Collection
    {
        $userId = $this->resolveUserId($userId);

        $models = [];

        $this->newInteractionQuery()
            ->where($this->userForeignKey(), $userId)
            ->where('type', InteractionTypeEnum::LIKE)
            ->get()
            ->groupBy('model_type')
            ->each(function (Collection $group, string $modelType) use (&$models) {
                /** @var class-string<Model> $modelType */
                $models = array_merge($models, (new $modelType())->newQuery()
                    ->whereIn('id', $group->pluck('model_id'))
                    ->get()
                    ->all());
            });

        return new Collection($models);
    }

    /**
     * @param  Collection<int, Model>  $models
     *
     * @return array<int|string, int>
     */
    public function likeCountsFor(Collection $models, InteractionTypeEnum $type = InteractionTypeEnum::LIKE): array
    {
        $counts = [];

        $models->groupBy(fn (Model $model) => $model->getMorphClass())
            ->each(function (Collection $group, string $modelType) use ($type, &$counts) {
                $this->newInteractionQuery()
                    ->toBase()
                    ->where('model_type', $modelType)
                    ->where('type', $type)
                    ->whereIn('model_id', $group->map(fn (Model $model) => $model->getKey()))
                    ->selectRaw('model_id, count(*) as aggregate')
                    ->groupBy('model_id')
                    ->get()
                    ->each(function (\stdClass $row) use (&$counts) {
                        $counts[$row->model_id] = is_numeric($row->aggregate) ? (int) $row->aggregate : 0;
                    });
            });

        return $counts;
    }

    /**
     * @param  Collection<int, Model>  $models
     *
     * @return Collection<string, Like>
     */
    public function userInteractionsFor(Collection $models, ?int $userId = null): Collection
    {
        $userId = $this->resolveUserId($userId);
        $result = new Collection();

        $models->groupBy(fn (Model $model) => $model->getMorphClass())
            ->each(function (Collection $group, string $modelType) use ($userId, &$result) {
                $this->newInteractionQuery()
                    ->where($this->userForeignKey(), $userId)
                    ->where('model_type', $modelType)
                    ->whereIn('model_id', $group->map(fn (Model $model) => $model->getKey()))
                    ->get()
                    ->each(function (Like $like) use ($modelType, &$result) {
                        $result->put($modelType.':'.$like->model_id, $like);
                    });
            });

        return $result;
    }

    protected function setInteraction(Model $model, InteractionTypeEnum $type, ?int $userId): Like
    {
        $this->assertModel($model);
        $userId = $this->resolveUserId($userId);

        return DB::transaction(function () use ($model, $type, $userId) {
            $existing = $this->findInteractionByType($model, $userId, $type);

            if ($existing !== null) {
                return $existing;
            }

            $this->clearOtherTypes($model, $userId, $type);

            $created = $this->createInteraction($model, $userId, $type);
            $this->forgetCounts($model);

            return $created;
        });
    }

    protected function removeInteraction(Model $model, InteractionTypeEnum $type, ?int $userId): bool
    {
        $this->assertModel($model);
        $userId = $this->resolveUserId($userId);

        return DB::transaction(function () use ($model, $type, $userId) {
            $existing = $this->findInteractionByType($model, $userId, $type);
            $removed = $existing !== null && $this->removeRecord($existing);

            if ($removed) {
                $this->forgetCounts($model);
            }

            return $removed;
        });
    }

    protected function hasInteraction(Model $model, InteractionTypeEnum $type, ?int $userId): bool
    {
        $this->assertModel($model);
        $userId = $this->resolveUserId($userId);

        return $this->newInteractionQuery()
            ->where($this->userForeignKey(), $userId)
            ->where('model_id', $model->getKey())
            ->where('model_type', $model->getMorphClass())
            ->where('type', $type)
            ->exists();
    }

    protected function countForType(Model $model, InteractionTypeEnum $type): int
    {
        $this->assertModel($model);

        $query = fn (): int => $this->newInteractionQuery()
            ->where('model_id', $model->getKey())
            ->where('model_type', $model->getMorphClass())
            ->where('type', $type)
            ->count();

        if (!$this->cacheEnabled()) {
            return $query();
        }

        return (int) Cache::remember($this->countCacheKey($model, $type), $this->cacheTtl(), $query);
    }

    protected function cacheEnabled(): bool
    {
        return (bool) config('like.cache.enabled', false);
    }

    protected function cacheTtl(): int
    {
        $ttl = config('like.cache.ttl');

        return is_numeric($ttl) ? (int) $ttl : 60;
    }

    protected function countCacheKey(Model $model, InteractionTypeEnum $type): string
    {
        return sprintf('like:count:%s:%s:%s', $type->value, $model->getMorphClass(), (string) $model->getKey());
    }

    /**
     * Invalidate the cached per-type counts for a model after its interactions change.
     */
    protected function forgetCounts(Model $model): void
    {
        if (!$this->cacheEnabled()) {
            return;
        }

        foreach (InteractionTypeEnum::getValues() as $type) {
            Cache::forget($this->countCacheKey($model, $type));
        }
    }

    protected function findInteraction(Model $model, int $userId): ?Like
    {
        /** @var null|Like $like */
        $like = $this->newInteractionQuery()
            ->where($this->userForeignKey(), $userId)
            ->where('model_id', $model->getKey())
            ->where('model_type', $model->getMorphClass())
            ->first();

        return $like;
    }

    protected function findInteractionByType(Model $model, int $userId, InteractionTypeEnum $type): ?Like
    {
        /** @var null|Like $like */
        $like = $this->newInteractionQuery()
            ->where($this->userForeignKey(), $userId)
            ->where('model_id', $model->getKey())
            ->where('model_type', $model->getMorphClass())
            ->where('type', $type)
            ->first();

        return $like;
    }

    protected function clearOtherTypes(Model $model, int $userId, InteractionTypeEnum $type): void
    {
        $this->newInteractionQuery()
            ->where($this->userForeignKey(), $userId)
            ->where('model_id', $model->getKey())
            ->where('model_type', $model->getMorphClass())
            ->where('type', '!=', $type)
            ->delete();
    }

    protected function createInteraction(Model $model, int $userId, InteractionTypeEnum $type): Like
    {
        /** @var Like $like */
        $like = $this->newInteractionQuery()->getModel();

        $like->fill([
            'model_id' => $model->getKey(),
            'model_type' => $model->getMorphClass(),
            'type' => $type,
        ]);
        $like->setAttribute($this->userForeignKey(), $userId);
        $like->save();

        return $like;
    }

    protected function updateType(Like $like, InteractionTypeEnum $type): Like
    {
        $like->update(['type' => $type]);

        return $like->refresh();
    }

    protected function removeRecord(Like $like): bool
    {
        return (bool) $like->delete();
    }

    protected function resolveUserId(?int $userId): int
    {
        $userId ??= (int) Auth::id();

        if ($userId <= 0) {
            throw new AuthenticationException('You must be authenticated to interact with models.');
        }

        return $userId;
    }

    /** @return Builder<Like> */
    protected function newInteractionQuery(): Builder
    {
        /** @var class-string<Like> $interactionModel */
        $interactionModel = $this->interactionModel();

        return $interactionModel::query();
    }

    protected function interactionModel(): string
    {
        return (string) (config('like.interaction_model') ?? Like::class);
    }

    protected function userForeignKey(): string
    {
        return (string) (config('like.users.foreign_key') ?? 'user_id');
    }

    protected function assertModel(Model $model): void
    {
        if (!$model->exists) {
            throw new InvalidArgumentException('The model must be saved before it can be interacted with.');
        }
    }
}
