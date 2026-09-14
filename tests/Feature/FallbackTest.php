<?php

use CSlant\LaravelLike\Models\Like;
use CSlant\LaravelLike\Tests\Models\Post;
use CSlant\LaravelLike\Tests\Models\User;

test('interaction model falls back to Like when config is null', function () {
    config()->set('like.interaction_model', null);

    $post = Post::create(['title' => 'Fallback']);

    expect($post->likes()->getRelated())->toBeInstanceOf(Like::class);
});

test('user model falls back to auth provider model when like.users.model is null', function () {
    config()->set('like.users.model', null);
    config()->set('auth.providers.users.model', User::class);

    $like = new Like();

    expect($like->user()->getRelated())->toBeInstanceOf(User::class);
});
