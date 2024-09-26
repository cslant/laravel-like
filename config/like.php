<?php

return [
    'name' => 'The interactions configuration',

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
         * Use this to set the user model class for the user relationship.
         */
        'model' => 'App\Models\User',

        /*
         * User tables foreign key name.
         * Use this to set the foreign key name for the user relationship.
         */
        'foreign_key' => 'user_id',
    ],
];
