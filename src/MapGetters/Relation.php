<?php

namespace Exper\FilterBuilder\MapGetters;

class Relation
{
    private static $maps;

    public static function get(string $primary, string $relation)
    {
        if (!isset(static::$maps[$primary])) {
            static::$maps[$primary] = config('relations.'.$primary) ?? [];
        }

        return static::$maps[$primary][$relation] ?? null;
    }
}
