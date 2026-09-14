<?php

use CSlant\LaravelLike\LikeManager;
use CSlant\LaravelLike\Tests\Models\Post;
use CSlant\LaravelLike\Tests\Models\User;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    config()->set('like.cache.enabled', true);
    $this->user = User::create();
    $this->post = Post::create(['title' => 'Cached Post']);
    $this->actingAs($this->user);
});

test('likesCount hits the database once then serves cached value', function () {
    app(LikeManager::class)->like($this->post);

    expect(app(LikeManager::class)->likesCount($this->post))->toBe(1);

    DB::enableQueryLog();
    app(LikeManager::class)->likesCount($this->post);
    app(LikeManager::class)->likesCount($this->post);

    expect(DB::getQueryLog())->toHaveCount(0);
});

test('cache is invalidated when the interaction changes', function () {
    app(LikeManager::class)->like($this->post);
    expect(app(LikeManager::class)->likesCount($this->post))->toBe(1);

    app(LikeManager::class)->unlike($this->post);

    expect(app(LikeManager::class)->likesCount($this->post))->toBe(0);
});

test('cache is invalidated when toggle moves an interaction', function () {
    app(LikeManager::class)->dislike($this->post);
    expect(app(LikeManager::class)->dislikesCount($this->post))->toBe(1);

    app(LikeManager::class)->toggle($this->post);

    expect(app(LikeManager::class)->dislikesCount($this->post))->toBe(0)
        ->and(app(LikeManager::class)->likesCount($this->post))->toBe(1);
});

test('counts are not cached when the cache flag is disabled', function () {
    config()->set('like.cache.enabled', false);

    app(LikeManager::class)->like($this->post);
    app(LikeManager::class)->likesCount($this->post);

    DB::enableQueryLog();
    app(LikeManager::class)->likesCount($this->post);

    expect(DB::getQueryLog())->toHaveCount(1);
});
