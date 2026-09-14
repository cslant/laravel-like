<?php

namespace CSlant\LaravelLike\Providers;

use CSlant\LaravelLike\Contracts\LikeManager as LikeManagerContract;
use CSlant\LaravelLike\LikeManager;
use Illuminate\Support\ServiceProvider;

class LikeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerAssetsPublishing();
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->registerConfigs();
        $this->registerLikeManager();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<string>
     */
    public function provides(): array
    {
        return [LikeManagerContract::class, 'like'];
    }

    /**
     * Register configs.
     */
    protected function registerConfigs(): void
    {
        $configPath = __DIR__.'/../../config/like.php';
        $this->mergeConfigFrom($configPath, 'like');
    }

    /**
     * Register the like manager as a singleton.
     */
    protected function registerLikeManager(): void
    {
        $this->app->singleton(LikeManagerContract::class, LikeManager::class);
        $this->app->alias(LikeManagerContract::class, 'like');
    }

    /**
     * Register assets publishing.
     */
    public function registerAssetsPublishing(): void
    {
        $configPath = __DIR__.'/../../config/like.php';
        $this->publishes([
            $configPath => config_path('like.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../../migrations' => database_path('migrations'),
        ], 'migrations');
    }
}
