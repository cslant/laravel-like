<?php

use CSlant\LaravelLike\LikeManager;
use CSlant\LaravelLike\Models\Like;
use CSlant\LaravelLike\Tests\Models\Post;
use CSlant\LaravelLike\Tests\Models\User;
use CSlant\LaravelLike\Tests\Models\Video;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->user = User::create();
    $this->post = Post::create(['title' => 'Post One']);
    $this->post->push();
    $this->actingAs($this->user);
});

test('like creates a like interaction', function () {
    $like = app(LikeManager::class)->like($this->post);

    expect($like)->toBeInstanceOf(Like::class)
        ->and($this->post->likes()->count())->toBe(1)
        ->and($like->type->isLike())->toBeTrue()
        ->and($like->user_id)->toBe($this->user->id);
});

test('like is idempotent and moves dislike to like', function () {
    app(LikeManager::class)->like($this->post);
    $again = app(LikeManager::class)->like($this->post);

    expect($this->post->likes()->count())->toBe(1)
        ->and($again->type->isLike())->toBeTrue();

    app(LikeManager::class)->dislike($this->post);
    $moved = app(LikeManager::class)->like($this->post);

    expect($this->post->likes()->count())->toBe(1)
        ->and($moved->type->isLike())->toBeTrue()
        ->and($this->post->likes()->first()->type->isDislike())->toBeFalse();
});

test('dislike and love move existing interactions', function () {
    app(LikeManager::class)->like($this->post);

    app(LikeManager::class)->dislike($this->post);
    expect($this->post->likes()->first()->type->isDislike())->toBeTrue()
        ->and($this->post->likes()->count())->toBe(1);

    app(LikeManager::class)->love($this->post);
    expect($this->post->likes()->first()->type->isLove())->toBeTrue()
        ->and($this->post->likes()->count())->toBe(1);
});

test('unlike, unlove, and unDislike remove their rows', function () {
    app(LikeManager::class)->like($this->post);
    expect(app(LikeManager::class)->unlike($this->post))->toBeTrue()
        ->and($this->post->likes()->count())->toBe(0)
        ->and(app(LikeManager::class)->unlike($this->post))->toBeFalse();

    app(LikeManager::class)->dislike($this->post);
    expect(app(LikeManager::class)->unDislike($this->post))->toBeTrue()
        ->and($this->post->likes()->count())->toBe(0)
        ->and(app(LikeManager::class)->unDislike($this->post))->toBeFalse();

    app(LikeManager::class)->love($this->post);
    expect(app(LikeManager::class)->unlove($this->post))->toBeTrue()
        ->and($this->post->likes()->count())->toBe(0)
        ->and(app(LikeManager::class)->unlove($this->post))->toBeFalse();
});

test('toggle follows none-less-dislike-like circuit', function () {
    expect(app(LikeManager::class)->toggle($this->post)->type->isLike())->toBeTrue();

    expect(app(LikeManager::class)->toggle($this->post))->toBeNull()
        ->and($this->post->likes()->count())->toBe(0);

    app(LikeManager::class)->toggle($this->post); // like
    app(LikeManager::class)->toggle($this->post); // remove
    app(LikeManager::class)->dislike($this->post);
    expect(app(LikeManager::class)->toggle($this->post)->type->isLike())->toBeTrue();
});

test('toggle leaves love untouched', function () {
    app(LikeManager::class)->love($this->post);

    expect(app(LikeManager::class)->toggle($this->post)->type->isLove())->toBeTrue()
        ->and($this->post->likes()->count())->toBe(1);
});

test('interaction predicates check user state', function () {
    expect(app(LikeManager::class)->isLiked($this->post))->toBeFalse();

    app(LikeManager::class)->like($this->post);

    expect(app(LikeManager::class)->isLiked($this->post))->toBeTrue()
        ->and(app(LikeManager::class)->isDisliked($this->post))->toBeFalse()
        ->and(app(LikeManager::class)->isLoved($this->post))->toBeFalse();
});

test('counts are per type and total', function () {
    $u1 = User::create();
    $u2 = User::create();
    $u3 = User::create();

    app(LikeManager::class)->like($this->post, $u1->id);
    app(LikeManager::class)->dislike($this->post, $u2->id);
    app(LikeManager::class)->love($this->post, $u3->id);

    expect(app(LikeManager::class)->likesCount($this->post))->toBe(1)
        ->and(app(LikeManager::class)->dislikesCount($this->post))->toBe(1)
        ->and(app(LikeManager::class)->lovesCount($this->post))->toBe(1)
        ->and(app(LikeManager::class)->totalCount($this->post))->toBe(3);
});

test('userInteractions and userLikedModels scope by user', function () {
    $other = User::create();
    app(LikeManager::class)->like($this->post, $this->user->id);
    app(LikeManager::class)->like($this->post, $other->id);

    expect(app(LikeManager::class)->userInteractions($this->user->id))->toHaveCount(1)
        ->and(app(LikeManager::class)->userLikedModels($this->user->id)->first()->is($this->post))->toBeTrue();
});

test('userLikedModels avoids per-model queries', function () {
    $videos = collect(range(1, 3))->map(fn () => Video::create(['title' => 'T', 'duration' => 1]));
    Post::create(['title' => 'Post Two']);

    foreach ($videos as $video) {
        app(LikeManager::class)->like($video, $this->user->id);
    }
    app(LikeManager::class)->like($this->post, $this->user->id);

    DB::enableQueryLog();
    $models = app(LikeManager::class)->userLikedModels($this->user->id);

    expect($models)->toHaveCount(4)
        ->and(DB::getQueryLog())->toHaveCount(3)
        ->and($models->contains(fn (Model $m) => $m->is($this->post)))->toBeTrue()
        ->and($models->map(fn (Model $m) => $m::class)->unique()->count())->toBe(2);
});

test('explicit userId works without auth', function () {
    auth()->logout();
    $user = User::create();

    app(LikeManager::class)->like($this->post, $user->id);

    expect(app(LikeManager::class)->isLiked($this->post, $user->id))->toBeTrue();
});

test('unauthenticated action throws AuthenticationException', function () {
    auth()->logout();

    expect(fn () => app(LikeManager::class)->like($this->post))
        ->toThrow(AuthenticationException::class);
});

test('likes use integer ids unless is_uuids is enabled', function () {
    expect((new Like())->usesUniqueIds())->toBeFalse();

    config()->set('like.is_uuids', true);

    expect((new Like())->usesUniqueIds())->toBeTrue();
});
