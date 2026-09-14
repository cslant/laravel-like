<?php

return [
    /*
     * The flag to determine if the interactions table should use UUIDs.
     * If you want to use UUIDs instead of auto-incrementing integers for your interactions table, set this to true.
     */
    'is_uuids' => false,

    /*
     * The table name for interaction records.
     */
    'table_name' => 'likes',

    /*
     * The model class for the interaction table.
     */
    'interaction_model' => 'CSlant\LaravelLike\Models\Like',

    /*
     * The model and foreign key for the user relationship.
     */
    'users' => [
        /*
         * User model class.
         * When null, the package falls back to config('auth.providers.users.model').
         */
        'model' => null,

        /*
         * User tables foreign key name.
         * Use this to set the foreign key name for the user relationship.
         */
        'foreign_key' => 'user_id',
    ],

    /*
     * Caching for per-type interaction counts (likesCount, dislikesCount, lovesCount).
     * Counts are read far more often than they change, so caching them cuts repeated
     * COUNT queries on hot paths (feeds, listings). Disabled by default to keep the
     * package's out-of-the-box behaviour always consistent; enable it once your cache
     * store is configured.
     */
    'cache' => [
        'enabled' => false,

        /*
         * Time-to-live in seconds for a cached count. The cache is also actively
         * invalidated whenever an interaction is created, moved, or removed, so this
         * TTL is only a safety net.
         */
        'ttl' => 60,
    ],
];
