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
            return $this->createInteraction($model, $userId, InteractionTypeEnum::LIKE);
        }

        return match (true) {
            $current->type->isLike() => $this->removeRecord($current) ? null : $current,
            $current->type->isDislike() => $this->updateType($current, InteractionTypeEnum::LIKE),
            default => $current,
        };
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

    public function userInteractions(?int $userId = null): Collection
    {
        $userId = $this->resolveUserId($userId);

        return $this->newInteractionQuery()
            ->where($this->userForeignKey(), $userId)
            ->get();
    }

    public function userLikedModels(?int $userId = null): Collection
    {
        $userId = $this->resolveUserId($userId);

        return $this->newInteractionQuery()
            ->where($this->userForeignKey(), $userId)
            ->where('type', InteractionTypeEnum::LIKE)
            ->get()
            ->groupBy('model_type')
            ->map(function (Collection $likes, string $modelType) {
                /** @var class-string<Model> $modelType */
                return (new $modelType())->newQuery()
                    ->whereIn('id', $likes->pluck('model_id'))
                    ->get();
            })
            ->flatten(1)
            ->values();
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

            return $this->createInteraction($model, $userId, $type);
        });
    }

    protected function removeInteraction(Model $model, InteractionTypeEnum $type, ?int $userId): bool
    {
        $this->assertModel($model);
        $userId = $this->resolveUserId($userId);

        return DB::transaction(function () use ($model, $type, $userId) {
            $existing = $this->findInteractionByType($model, $userId, $type);

            return $existing !== null ? $this->removeRecord($existing) : false;
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

        return $this->newInteractionQuery()
            ->where('model_id', $model->getKey())
            ->where('model_type', $model->getMorphClass())
            ->where('type', $type)
            ->count();
    }

    protected function findInteraction(Model $model, int $userId): ?Like
    {
        /** @var Like|null $like */
        $like = $this->newInteractionQuery()
            ->where($this->userForeignKey(), $userId)
            ->where('model_id', $model->getKey())
            ->where('model_type', $model->getMorphClass())
            ->first();

        return $like;
    }

    protected function findInteractionByType(Model $model, int $userId, InteractionTypeEnum $type): ?Like
    {
        /** @var Like|null $like */
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
        $like = $this->newInteractionQuery()->create([
            $this->userForeignKey() => $userId,
            'model_id' => $model->getKey(),
            'model_type' => $model->getMorphClass(),
            'type' => $type,
        ]);

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
        if (! $model->exists) {
            throw new InvalidArgumentException('The model must be saved before it can be interacted with.');
        }
    }
}
