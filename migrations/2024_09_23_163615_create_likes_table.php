<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(config('like.table_name'), function (Blueprint $table) {
            if (config('like.is_uuids')) {
                $table->uuid('id')->primary();
                $table->morphs('model', null, 'model', 'uuid');
            } else {
                $table->id();
                $table->morphs('model');
            }

            $table->unsignedBigInteger(config('like.users.foreign_key'))->index();
            $table->string('type')->default('like');

            $table->unique([config('like.users.foreign_key'), 'model_id', 'model_type', 'type'], 'unique_user_model_type_interaction');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('like.table_name'));
    }
};
