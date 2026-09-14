<?php

use CSlant\LaravelLike\Tests\Models\Podcast;
use CSlant\LaravelLike\Tests\Models\User;
use CSlant\LaravelLike\Tests\Models\Video;

test('HasLike + HasLove compose without collision', function () {
    $podcast = Podcast::create(['title' => 'Episode']);

    expect(method_exists($podcast, 'likeTo'))->toBeTrue()
        ->and(method_exists($podcast, 'loveTo'))->toBeTrue()
        ->and(method_exists($podcast, 'likesCount'))->toBeTrue()
        ->and(method_exists($podcast, 'lovesCount'))->toBeTrue();
});

test('HasLove exposes love relations and counts', function () {
    $video = Video::create(['title' => 'T', 'duration' => 10]);

    expect(method_exists($video, 'loveTo'))->toBeTrue()
        ->and($video->lovesCount())->toBe(0);
});

test('UserHasInteraction remembers and forgets interactions', function () {
    $user = User::create();
    $user->likes()->create(['model_id' => 1, 'model_type' => 'App\Models\Post', 'type' => 'like']);

    expect($user->likes()->count())->toBe(1);

    $user->forgetInteractions('like');

    expect($user->likes()->count())->toBe(0);
});
