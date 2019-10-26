<?php

namespace Exper\FilterBuilder;

use Exper\FilterBuilder\Enums\Comparator;
use Exper\FilterBuilder\Kernel\FilterBuilder;
use Exper\FilterBuilder\MapGetters\Transfer;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

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
