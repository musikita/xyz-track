<?php

namespace Suitcorecms\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;
use Suitcorecms\Medialibrary\Media;

class SuitcorecmsServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $suitcorecmsConfigPath = __DIR__.'/../../config/suitcorecms.php';
        $this->mergeConfigFrom($suitcorecmsConfigPath, 'suitcorecms');

        $suitcoresiteConfigPath = __DIR__.'/../../config/suitcoresite.php';
        $this->mergeConfigFrom($suitcoresiteConfigPath, 'suitcoresite');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $suitcorecmsConfigPath = __DIR__.'/../../config/suitcorecms.php';
        $suitcoresiteConfigPath = __DIR__.'/../../config/suitcoresite.php';
        $this->publishes([
            $suitcorecmsConfigPath  => config_path('suitcorecms.php'),
            $suitcoresiteConfigPath => config_path('suitcoresite.php'),
        ], 'config');

        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../../robots/', 'suitcorecms-robot');
        $this->loadViewsFrom(__DIR__.'/../../themes/'.config('suitcorecms.theme'), 'suitcorecms');

        if ($this->app->runningInConsole()) {
            $this->commands([
                \Suitcorecms\Sites\Settings\Console\SettingSeedCommand::class,
                \Suitcorecms\Sites\Newsletters\Console\SendingNewsletterCommand::class,
                \Suitcorecms\Sites\Newsletters\Console\SendingNewsletterToCommand::class,
            ]);
        }

        if ($time = config('suitcoresite.newsletters.send_at', false)) {
            $schedule = $this->app->make(Schedule::class);
            $schedule->command('suitcorecms:newsletter-send')->at($time);
        }

        config(['medialibrary.media_model' => Media::class]);

        require __DIR__.'/../../helpers/cms_helpers.php';
        require __DIR__.'/../../helpers/site_helpers.php';
    }
}
