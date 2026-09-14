<?php

use CSlant\LaravelLike\Models\Like;

test('config defaults are merged on register', function () {
    expect(config('like.table_name'))->toBe('likes')
        ->and(config('like.interaction_model'))->toBe(Like::class)
        ->and(config('like.users.foreign_key'))->toBe('user_id')
        ->and(config('like.is_uuids'))->toBeFalse();
});
