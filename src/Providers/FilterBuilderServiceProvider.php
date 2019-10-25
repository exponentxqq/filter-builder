<?php

namespace Exper\FilterBuilder\Providers;

use Illuminate\Support\ServiceProvider;

class FilterBuilderServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([__DIR__.'/../configs/relations.php' => config_path('relations.php')], 'filter-builder');
        $this->publishes([__DIR__.'/../configs/transfers.php' => config_path('transfers.php')], 'filter-builder');
    }

    public function register()
    {
    }
}