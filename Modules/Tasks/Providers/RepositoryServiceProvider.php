<?php

namespace Modules\Tasks\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind(
            'Modules\Tasks\Repositories\Contracts\TasksRepositoryInterface',
            'Modules\Tasks\Repositories\TasksRepository'
        );
    }
}