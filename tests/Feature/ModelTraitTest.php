<?php

use CSlant\LaravelLike\Models\Like;
use CSlant\LaravelLike\Tests\Models\Post;
use CSlant\LaravelLike\Tests\Models\User;

beforeEach(function () {
    $this->post = Post::create(['title' => 'Trait Post']);
    $this->actingAs(User::create());
});

test('model helpers create interactions', function () {
    expect($this->post->like())->toBeInstanceOf(Like::class)
        ->and($this->post->dislike()->type->isDislike())->toBeTrue()
        ->and($this->post->love()->type->isLove())->toBeTrue()
        ->and($this->post->likesCount())->toBe(0)
        ->and($this->post->dislikesCount())->toBe(0)
        ->and($this->post->lovesCount())->toBe(1);
});

test('model helpers toggle and remove', function () {
    expect($this->post->toggle()->type->isLike())->toBeTrue()
        ->and($this->post->toggle())->toBeNull();

    $this->post->like();

    expect($this->post->unlike())->toBeTrue()
        ->and($this->post->unlike())->toBeFalse();
});

test('model predicates reflect user state', function () {
    expect($this->post->isLiked())->toBeFalse();

    $this->post->like();

    expect($this->post->isLiked())->toBeTrue()
        ->and($this->post->isDisliked())->toBeFalse()
        ->and($this->post->isLoved())->toBeFalse();
});
