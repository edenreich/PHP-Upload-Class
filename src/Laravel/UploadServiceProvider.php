<?php

namespace Reich\Upload\Laravel;

use Reich\Upload;
use Illuminate\Support\ServiceProvider;

class UploadServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Upload::class);

        $this->app->alias('upload', Upload::class);
    }
}