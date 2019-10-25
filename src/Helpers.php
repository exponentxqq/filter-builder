<?php

namespace Exper\FilterBuilder;

class Helpers
{
    public static function formatField(string $field, ?string $table)
    {
        if (false !== strpos($field, '.')) {
            return str_replace([' ', '.', '_'], '', ucwords($field, ' ._'));
        } else {
            return str_replace([' ', '.', '_'], '', ucwords("{$table}.{$field}", ' ._'));
        }
    }
}

