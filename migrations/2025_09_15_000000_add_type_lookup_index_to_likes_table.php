<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * Adds a composite index covering the columns used by count queries
     * that are not scoped to a user (likesCount, dislikesCount, lovesCount,
     * totalCount, likeCountsFor), which the unique index on
     * (user_id, model_id, model_type, type) does not serve.
     */
    public function up(): void
    {
        Schema::table(config('like.table_name'), function (Blueprint $table) {
            $table->index(['model_type', 'model_id', 'type'], 'like_type_lookup_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(config('like.table_name'), function (Blueprint $table) {
            $table->dropIndex('like_type_lookup_index');
        });
    }
};
