<?php

namespace Exper\FilterBuilder\Formatter;

class FilterFormatter
{
    public static function format($table, $filters)
    {
        if (empty($filters)) {
            return [];
        }
        ArrayFormatter::format($table, $filters);
        return $filters;
    }
}
