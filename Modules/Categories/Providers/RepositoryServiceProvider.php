<?php

namespace Modules\Categories\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind(
            'Modules\Categories\Repositories\Contracts\CategoriesRepositoryInterface',
            'Modules\Categories\Repositories\CategoriesRepository'
        );

    }
}