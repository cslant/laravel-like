<?php

return [
    'name' => 'Like',
    'models' => [
        'like' => \CSlant\LaravelLike\Models\Like::class,
    ],

    /*
     * The flag to determine if the likes table should use UUIDs.
     * If you want to use UUIDs instead of auto-incrementing integers for your likes table, set this to true.
     */
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
