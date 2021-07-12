<?php

namespace tanyudii\YinMedia;

use Illuminate\Support\ServiceProvider;
use tanyudii\YinMedia\Services\YinMediaService;

class YinMediaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind("yin-media-service", function () {
            return new YinMediaService();
        });

        $this->mergeConfigFrom(
            __DIR__ . "/../assets/yin-media.php",
            "yin-media"
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes(
                [
                    __DIR__ . "/../assets/migrations" => \database_path(
                        "migrations"
                    ),
                ],
                "yin-media-migrations"
            );

            $this->publishes(
                [
                    __DIR__ . "/../assets/yin-media.php" => \config_path(
                        "yin-media.php"
                    ),
                ],
                "yin-media-config"
            );
        }
    }
}
