<?php

namespace Exper\FilterBuilder\Formatter;

use Exper\FilterBuilder\Enums\Comparator;
use Exper\FilterBuilder\Exceptions\InvalidFiltersException;
use Exper\FilterBuilder\FilterStore;

class NormalFormatter
{
    public static function format($table, &$filter)
    {
        if ($filter !== 'or' && $filter !== 'and') {
            $count = count($filter);
            if ($count < 2) {
                throw new InvalidFiltersException("invalid filter: " . implode(', ', $filter));
            } elseif (in_array($filter[1], Comparator::values())) {
                $filter = new FilterStore($table, $filter[0], $filter[2] ?? null, $filter[1]);
            } else {
                $filter = new FilterStore($table, $filter[0], $filter[1]);
            }
        }
    }
}
