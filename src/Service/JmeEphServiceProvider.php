<?php

declare(strict_types=1);

namespace JmeEph\Service;

use Illuminate\Support\ServiceProvider;
use Override;
use JmeEph\FFI\JmeEphFFI;

/**
 * Laravel Service Provider for JPL Moshier Ephemeris FFI.
 *
 * Registers JmeEphFFI as a singleton in the Laravel service container,
 * ensuring the JPL Moshier Ephemeris library is loaded once and shared across
 * the entire application lifecycle.
 *
 * Features:
 * - Singleton registration (one instance per request)
 * - Automatic configuration merging
 * - Publishable config and native library files
 * - Facade alias support
 *
 * @author Jayesh Patel <jayeshmepani777@gmail.com>
 *
 * @see \JmeEph\FFI\JmeEphFFI
 */
final class JmeEphServiceProvider extends ServiceProvider
{
    /**
     * Register application services.
     *
     * Merges package configuration and registers JmeEphFFI as singleton.
     * The singleton ensures only one FFI instance exists per request,
     * preventing multiple library loads and memory waste.
     */
    #[Override]
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/jmeeph.php',
            'jmeeph'
        );

        $this->app->singleton('jmeeph', function ($app) {
            $libraryPath = $app->make('config')->get('jmeeph.library_path');
            return new JmeEphFFI($libraryPath);
        });

        $this->app->singleton(JmeService::class, function ($app) {
            return new JmeService(
                $app->make(JmeEphFFI::class),
                $app->make('config')->get('jmeeph.calculation_path', 'native')
            );
        });

        $this->app->alias('jmeeph', JmeEphFFI::class);
    }

    /**
     * Bootstrap application services.
     *
     * Registers publishable resources:
     * - config/jmeeph.php → config_path('jmeeph.php')
     * - current platform native library → storage_path('app/jmeeph/<library>')
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/jmeeph.php' => config_path('jmeeph.php'),
        ], 'jmeeph-config');

        $library = $this->findPublishableLibrary();
        if ($library !== null) {
            $this->publishes([
                $library => storage_path('app/jmeeph/' . basename($library)),
            ], 'jmeeph-library');
        }
    }

    /**
     * Get provided services.
     *
     * @return array<int, string>
     */
    #[Override]
    public function provides(): array
    {
        return ['jmeeph', JmeEphFFI::class, JmeService::class];
    }

    private function findPublishableLibrary(): ?string
    {
        $family = PHP_OS_FAMILY;
        $arch = strtolower(php_uname('m'));
        $arch = match (true) {
            in_array($arch, ['x86_64', 'amd64'], true) => 'x64',
            in_array($arch, ['aarch64', 'arm64'], true) => 'arm64',
            default => $arch,
        };

        $file = match ($family) {
            'Windows' => 'jme.dll',
            'Darwin' => 'libjme.dylib',
            default => 'libjme.so',
        };

        $candidateDirs = [
            __DIR__ . '/../../libs/' . match ($family) {
                'Windows' => 'windows-' . $arch,
                'Darwin' => 'macos-' . $arch,
                default => 'linux-' . $arch,
            },
            __DIR__ . '/../../build',
        ];

        foreach ($candidateDirs as $dir) {
            $path = $dir . '/' . $file;
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }
}
