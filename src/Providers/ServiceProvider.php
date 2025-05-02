<?php

namespace KhanhDuy\DanhmuchanhchinhvnMaps\Providers;

use KhanhDuy\DanhmuchanhchinhvnMaps\Console\Commands\ImportCommand;
use KhanhDuy\DanhmuchanhchinhvnMaps\Console\Commands\InstallCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use KhanhDuy\DanhmuchanhchinhvnMaps\Helpers\DownloadFile;
use KhanhDuy\DanhmuchanhchinhvnMaps\Imports\GSOImports;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(DownloadFile::class, fn($app) => new DownloadFile());
        $this->app->singleton(GSOImports::class, fn($app) => new GSOImports());

        $this->mergeConfigFrom(
            dirname(__DIR__, 2) . '/config/gso.php',
            'gso'
        );
    }

    public function boot(Filesystem $filesystem): void
    {
        $this->publishes([
            dirname(__DIR__, 2) . '/config/gso.php' => config_path('gso.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../database/migrations/create_danhmuchanhchinhvn-maps_table.php' => $this->generateMigrationFileName($filesystem),
        ], 'migrations');

        $this->commands([
            InstallCommand::class,
            ImportCommand::class
        ]);
    }

    protected function generateMigrationFileName(Filesystem $filesystem): string
    {
        $timestamp = date('Y_m_d_His');
        return Collection::make([
            $this->app->databasePath() . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR
        ])
            ->flatMap(fn($path) => $filesystem->glob($path . '*create_danhmuchanhchinhvn-maps_table.php'))
            ->push($this->app->databasePath() . "/migrations/{$timestamp}_create_danhmuchanhchinhvn-maps_table.php")
            ->first();

    }
}
