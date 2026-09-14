<?php

use CSlant\LaravelLike\Enums\InteractionTypeEnum;
use CSlant\LaravelLike\LikeManager;
use CSlant\LaravelLike\Tests\Models\Post;
use CSlant\LaravelLike\Tests\Models\User;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->user = User::create();
    $this->posts = collect(range(1, 3))->map(fn (int $i) => Post::create(['title' => "Post {$i}"]));
});

test('likeCountsFor returns per-model counts with a single query', function () {
    $u1 = User::create();
    $u2 = User::create();

    app(LikeManager::class)->like($this->posts[0], $this->user->id);
    app(LikeManager::class)->like($this->posts[0], $u1->id);
    app(LikeManager::class)->like($this->posts[1], $u2->id);
    // posts[2] has no likes

    DB::enableQueryLog();
    $counts = app(LikeManager::class)->likeCountsFor($this->posts, InteractionTypeEnum::LIKE);

    expect(DB::getQueryLog())->toHaveCount(1)
        ->and($counts[$this->posts[0]->getKey()])->toBe(2)
        ->and($counts[$this->posts[1]->getKey()])->toBe(1)
        ->and($counts)->not->toHaveKey($this->posts[2]->getKey());
});

test('userInteractionsFor returns only the given user interactions with a single query', function () {
    $other = User::create();

    app(LikeManager::class)->like($this->posts[0], $this->user->id);
    app(LikeManager::class)->dislike($this->posts[1], $this->user->id);
    app(LikeManager::class)->like($this->posts[1], $other->id);
    // posts[2] has no interaction from $this->user

    DB::enableQueryLog();
    $interactions = app(LikeManager::class)->userInteractionsFor($this->posts, $this->user->id);

    $key0 = $this->posts[0]->getMorphClass().':'.$this->posts[0]->getKey();
    $key1 = $this->posts[1]->getMorphClass().':'.$this->posts[1]->getKey();
    $key2 = $this->posts[2]->getMorphClass().':'.$this->posts[2]->getKey();

    expect(DB::getQueryLog())->toHaveCount(1)
        ->and($interactions->get($key0)->type->isLike())->toBeTrue()
        ->and($interactions->get($key1)->type->isDislike())->toBeTrue()
        ->and($interactions->has($key2))->toBeFalse();
});
