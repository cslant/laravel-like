<?php

namespace CSlant\LaravelLike\Providers;

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
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<string>|null
     */
    public function provides(): ?array
    {
        return ['like'];
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
