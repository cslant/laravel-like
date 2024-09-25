<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(config('like.table_name'), function (Blueprint $table) {
            $table->id();

            if (config('like.is_uuids')) {
                $table->uuid()->index();
            }

            $table->unsignedBigInteger(config('like.user_foreign_key'))->index();
            $table->morphs('model');
            $table->string('type')->default('like');

            $table->unique(['user_id', 'model_id', 'model_type', 'type'], 'unique_user_model_type_interaction');

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
