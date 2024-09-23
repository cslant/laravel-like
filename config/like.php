<?php

return [
    'name' => 'Like',
    'models' => [
        'like' => \CSlant\LaravelLike\Models\Like::class,
    ],

    'is_uuids' => false,

    /*
     * The table name for likes records.
     */
    'likes_table' => 'likes',

    /*
     * User tables foreign key name.
     */
    'user_foreign_key' => 'user_id',
];
