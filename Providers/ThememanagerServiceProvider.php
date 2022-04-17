<?php

/**
 * Putting this here to help remind you where this came from.
 *
 * I'll get back to improving this and adding more as time permits
 * if you need some help feel free to drop me a line.
 *
 * * Twenty-Years Experience
 * * PHP, JavaScript, Laravel, MySQL, Java, Python and so many more!
 *
 *
 * @author  Simple-Pleb <plebeian.tribune@protonmail.com>
 * @website https://www.simple-pleb.com
 * @source https://github.com/simplepleb/thememanager-module
 *
 * @license Free to do as you please
 *
 * @since 1.0
 *
 */

namespace Modules\Thememanager\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Thememanager\Entities\SiteTheme;
use Symfony\Component\Finder\Finder;
use Illuminate\Support\Facades\Schema;

use Illuminate\Database\Eloquent\Factory;

class ThememanagerServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Thememanager';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'thememanager';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));

        // adding global middleware
        $kernel = $this->app->make('Illuminate\Contracts\Http\Kernel');
        $kernel->pushMiddleware('Modules\Thememanager\Http\Middleware\GenerateMenus');

        // register commands
        $this->registerCommands('\Modules\Thememanager\Console');

        $path_to = module_path('Thememanager');

        $this->publishes([
            $path_to . '/Resources/views/components' => base_path('resources/views/components'),
        ], 'theme-manager');

        if ( Schema::hasTable('site_themes') ) {
            $theme = SiteTheme::where('active',1)->first();
            if( $theme ){

                $fields = \App\Models\CustomField::where('module', 'thememanager-'.$theme->slug)->get();
                foreach ($fields as $field) {
                    // $f[$field->field_name] = $field->field_value;
                    view()->share($field->field_name, $field->field_value);
                    /*view()->composer('*', function ($view) use ($field) {
                        $view->with($field->field_name, $field->field_value);
                    });*/
                }

            }
        }


    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'), $this->moduleNameLower
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);

        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->moduleNameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        /*$langPath = resource_path('lang/modules/thememanager');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'thememanager');
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
        }*/

        if ( Schema::hasTable('site_themes') ) {
            $active = SiteTheme::where('active', 1)->first();
            if($active) {
                $t_langPath = public_path('themes/'.$active->slug.'/lang');
                // dd( $t_langPath);
                if (is_dir($t_langPath)){
                    // $arr[] = $t_langPath;
                    $this->loadTranslationsFrom($t_langPath, $active->slug);
                }
                else {
                    $langPath = resource_path('lang/modules/thememanager');

                    if (is_dir($langPath)) {
                        $this->loadTranslationsFrom($langPath, 'thememanager');
                    } else {
                        $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
                    }
                }
            }
        }



    }

    /**
     * Register commands.
     *
     * @param string $namespace
     */
    protected function registerCommands($namespace = '')
    {
        $finder = new Finder(); // from Symfony\Component\Finder;
        $finder->files()->name('*.php')->in(__DIR__.'/../Console');

        $classes = [];
        foreach ($finder as $file) {
            $class = $namespace.'\\'.$file->getBasename('.php');
            array_push($classes, $class);
        }

        $this->commands($classes);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (\Config::get('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }
        return $paths;
    }
}
