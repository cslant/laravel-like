<?php

use Illuminate\Support\Facades\Schema;

test('likes table has a composite index for count lookups without a user filter', function () {
    $indexes = Schema::getIndexes(config('like.table_name'));

    $hasLookupIndex = collect($indexes)->contains(
        fn (array $index) => $index['columns'] === ['model_type', 'model_id', 'type']
    );

    expect($hasLookupIndex)->toBeTrue();
});
