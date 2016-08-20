<?php

namespace Core\Translate;

use Core\Translate\Console\Commands\BuildCache;
use Core\Translate\Console\Commands\DumpTranslation;
use Core\Translate\Console\Commands\FillTranslation;
use Illuminate\Support\ServiceProvider;

/**
 * Class TranslationServiceProvider
 * @package Core\Translate
 */
class TranslationServiceProvider extends ServiceProvider
{

    /**
     * @var bool
     */
    protected $defer = true;


    /**
     *
     */
    public function boot()
    {
        $this->publishConfig();
        $this->publishMigration();
    }


    /**
     *
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config.php', 'translation');

        $this->registerLoader();
        $this->registerTranslator();

        $this->registerFillTranslationCommand();
        $this->registerBuildCacheCommand();
        $this->registerDumpTranslationCommand();

        $this->commands('translation.command.fill-translation');
        $this->commands('translation.command.build-cache');
        $this->commands('translation.command.dump');
    }


    /**
     * Register DB/File Loader
     */
    protected function registerLoader()
    {
        $this->app->singleton('translation.loader', function ($app) {
            return new DatabaseLoader($app['cache'], config('translation.cache'), $app['files'], $app['path.lang']);
        });
    }

    /**
     * Register Translator
     */
    private function registerTranslator()
    {
        $this->app->singleton('translator', function ($app) {
            $loader = $app['translation.loader'];

            // When registering the translator component, we'll need to set the default
            // locale as well as the fallback locale. So, we'll grab the application
            // configuration so we can easily get both of these values from there.
            $locale = $app['config']['app.locale'];

            $trans = new Translator($loader, $locale, config('translation'));

            $trans->setFallback($app['config']['app.fallback_locale']);

            return $trans;
        });

    }

    /**
     * Register fill translation command
     */
    private function registerFillTranslationCommand()
    {
        $this->app->singleton('translation.command.fill-translation', function ($app) {
            return new FillTranslation();
        });
    }

    /**
     * Register buld cache command
     */
    private function registerBuildCacheCommand()
    {
        $this->app->singleton('translation.command.build-cache', function ($app) {
            return new BuildCache();
        });
    }

    /**
     *
     */
    private function registerDumpTranslationCommand()
    {
        $this->app->singleton('translation.command.dump', function ($app) {
            return new DumpTranslation();
        });
    }

    /**
     *
     */
    private function publishConfig()
    {
        $this->publishes([
            __DIR__ . '/config.php' => config_path('translation.php'),
        ]);
    }

    /**
     *
     */
    private function publishMigration()
    {
        $this->publishes([
            __DIR__ . '/database/migrations/' => database_path('migrations'),
        ], 'migrations');
    }


    /**
     * @return array
     */
    public function provides()
    {
        return [
            'translator',
            'translation.loader',
            'translation.command.fill-translation',
            'translation.command.build-cache',
        ];
    }
}
