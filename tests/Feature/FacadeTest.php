<?php

use CSlant\LaravelLike\Contracts\LikeManager;
use CSlant\LaravelLike\Facades\Like;
use CSlant\LaravelLike\Facades\Love;
use CSlant\LaravelLike\Models\Like as LikeModel;
use CSlant\LaravelLike\Tests\Models\Post;
use CSlant\LaravelLike\Tests\Models\User;

beforeEach(function () {
    $this->post = Post::create(['title' => 'Facade Post']);
    $this->actingAs(User::create());
});

test('Like facade proxies to formatLike manager action', function () {
    expect(Like::like($this->post))->toBeInstanceOf(LikeModel::class)
        ->and($this->post->likes()->count())->toBe(1);
});

test('Love facade shares the same manager', function () {
    Love::love($this->post);

    expect(Love::isLoved($this->post))->toBeTrue()
        ->and(Like::isLoved($this->post))->toBeTrue();
});

test('manager is bound as singleton and aliased to like', function () {
    expect(app('like'))->toBeInstanceOf(LikeManager::class)
        ->and(app(LikeManager::class))->toBe(app('like'));
});
