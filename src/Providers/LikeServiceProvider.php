<?php

namespace CSlant\Laravel\Like\Providers;

use Illuminate\Support\ServiceProvider;

class LikeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerMigration();
    }

    /**
     * Register services.
     *
     * @return void
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
     * Register the package's migrations.
     *
     * @return void
     */
    public function registerMigration(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../../migrations');
    }

    /**
     * Register configs.
     *
     * @return void
     */
    protected function registerConfigs(): void
    {
        $configPath = __DIR__.'/../../config/like.php';
        $this->mergeConfigFrom($configPath, 'like');
    }
}
