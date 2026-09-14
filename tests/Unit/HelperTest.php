<?php

test('count_digital formats large counts', function (int $count, int|string $expected) {
    expect(count_digital($count))->toBe($expected);
})->with([
    [999, 999],
    [1000, '1K'],
    [1500, '1.5K'],
    [999999, '1M'],
    [1000000, '1M'],
]);
