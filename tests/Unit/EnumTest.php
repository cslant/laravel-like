<?php

use CSlant\LaravelLike\Enums\InteractionTypeEnum;

test('getValues excludes neutral', function () {
    expect(InteractionTypeEnum::getValues())->toBe([
        InteractionTypeEnum::LIKE,
        InteractionTypeEnum::DISLIKE,
        InteractionTypeEnum::LOVE,
    ]);
});

test('getValuesAsStrings returns string values', function () {
    expect(InteractionTypeEnum::getValuesAsStrings())->toBe(['like', 'dislike', 'love']);
});

test('getTypeByValue maps strings to enum cases', function (string $value, InteractionTypeEnum $expected) {
    expect(InteractionTypeEnum::getTypeByValue($value))->toBe($expected);
})->with([
    ['like', InteractionTypeEnum::LIKE],
    ['dislike', InteractionTypeEnum::DISLIKE],
    ['love', InteractionTypeEnum::LOVE],
    ['unknown', InteractionTypeEnum::NEUTRAL],
]);

test('isValid accepts only non-null known values', function () {
    expect(InteractionTypeEnum::isValid(InteractionTypeEnum::LIKE))->toBeTrue()
        ->and(InteractionTypeEnum::isValid('love'))->toBeTrue()
        ->and(InteractionTypeEnum::isValid('neutral'))->toBeFalse()
        ->and(InteractionTypeEnum::isValid(null))->toBeFalse();
});
