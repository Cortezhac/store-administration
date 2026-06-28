<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

/**
 * Modular Livewire Service Provider
 *
 * With this provider we make a modular architecture of the Livewire components
 * This provider is responsible for registering all the Livewire components
 * of the application for each module
 *
 * We use this approach to keep the code organized and easy to maintain
 */
class ModularLivewireServiceProvider extends ServiceProvider
{
    // Module list: the directory name in the modules directory
    // Example: modules/{$module}/resources/views/components
    // This is the list of modules that will be registered
    protected array $modules = [
        'Category',
    ];

    public function boot(): void
    {
        foreach ($this->modules as $module) {
            $this->registerModule($module);
        }

        $this->registerSharedComponents();
    }

    protected function registerSharedComponents(): void
    {
        $path = base_path('modules/Shared/resources/views/components');

        if (is_dir($path)) {
            Blade::anonymousComponentPath($path, 'shared');
        }
    }

    protected function registerModule(string $module): void
    {
        $basePath = base_path("modules/{$module}");

        // Vistas del namespace (para views que no son componentes Livewire)
        $viewsPath = "{$basePath}/resources/views";
        if (is_dir($viewsPath)) {
            $this->loadViewsFrom($viewsPath, strtolower($module));
        }

        // Componentes Livewire (MFC discovery)
        $this->registerLivewireComponents($module, $basePath);

        // Migraciones
        $migrationsPath = "{$basePath}/Database/Migrations";
        if (is_dir($migrationsPath)) {
            $this->loadMigrationsFrom($migrationsPath);
        }
    }

    protected function registerLivewireComponents(string $module, string $basePath): void
    {
        // Namespace para MFC discovery
        // Esto hace que `category::index` resuelva a
        // modules/Category/resources/views/components/⚡index/
        $componentsPath = "{$basePath}/resources/views/components";
        if (is_dir($componentsPath)) {
            Livewire::addNamespace(
                namespace: strtolower($module),
                viewPath: $componentsPath,
            );
        }

        // Rutas
        $routesPath = "{$basePath}/routes/web.php";
        if (is_file($routesPath)) {
            $this->loadRoutesFrom($routesPath);
        }
    }
}
