<?php

use CSlant\LaravelLike\Tests\Models\Post;
use CSlant\LaravelLike\Tests\Models\User;

test('testbench environment is wired', function () {
    $user = User::create();
    $post = Post::create(['title' => 'Hello']);

    expect($user->exists)->toBeTrue()
        ->and($post->exists)->toBeTrue();
});
