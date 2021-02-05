<?php

declare(strict_types=1);

namespace Zing\LaravelBookmark;

use Illuminate\Support\ServiceProvider;

class BookmarkServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes(
                [
                    $this->getConfigPath() => config_path('bookmark.php'),
                ],
                'bookmark-config'
            );
            $this->publishes(
                [
                    $this->getMigrationsPath() => database_path('migrations'),
                ],
                'bookmark-migrations'
            );
            if ($this->shouldLoadMigrations()) {
                $this->loadMigrationsFrom($this->getMigrationsPath());
            }
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom($this->getConfigPath(), 'bookmark');
    }

    protected function getConfigPath(): string
    {
        return __DIR__ . '/../config/bookmark.php';
    }

    protected function getMigrationsPath(): string
    {
        return __DIR__ . '/../migrations';
    }

    private function shouldLoadMigrations(): bool
    {
        return (bool) config('bookmark.load_migrations');
    }
}
