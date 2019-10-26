<?php

namespace Exper\FilterBuilder\Formatter;

use Exper\FilterBuilder\Exceptions\InvalidFiltersException;

class FilterFormatter
{
    public static function format($table, $filters)
    {
        if (empty($filters)) {
            throw new InvalidFiltersException();
        }
        ArrayFormatter::format($table, $filters);
        return $filters;
    }
}
