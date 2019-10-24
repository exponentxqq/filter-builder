<?php

namespace Exper\FilterBuilder\MapGetters;

class Transfer
{
    private static $maps;

    public static function get(string $table, string $field)
    {
        if (!isset(static::$maps[$table])) {
            static::$maps[$table] = config('transfers.'.$table) ?? [];
        }
        return static::$maps[$table][$field] ?? $field;
    }
}
