<?php

namespace Exper\FilterBuilder\Facades;

use Illuminate\Support\Facades\Facade;

class Filter extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'filter-builder';
    }
}
