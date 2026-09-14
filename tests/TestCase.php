<?php

namespace CSlant\LaravelLike\Tests;

use CSlant\LaravelLike\Models\Like;
use CSlant\LaravelLike\Providers\LikeServiceProvider;
use CSlant\LaravelLike\Tests\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('like.users.model', User::class);
        config()->set('like.users.foreign_key', 'user_id');
        config()->set('like.interaction_model', Like::class);
    }

    protected function getPackageProviders($app): array
    {
        return [LikeServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('');
            $table->timestamps();
        });

        (include __DIR__.'/../migrations/2024_09_23_163615_create_likes_table.php')->up();
    }
}
