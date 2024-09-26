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
     * User tables foreign key name.
     */
    'user_foreign_key' => 'user_id',
];
