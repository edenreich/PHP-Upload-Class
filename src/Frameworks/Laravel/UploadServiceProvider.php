<?php

namespace Reich\Frameworks\Laravel;

use Reich\Upload;
use Illuminate\Support\ServiceProvider;

class UploadServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(Upload::class);

        $this->app->singleton('upload', Upload::class);
    }

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '../config/upload.php' => config_path('upload.php'),
        ]);
    }
}