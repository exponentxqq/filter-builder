<?php


namespace Exper\FilterBuilder\src\Formatter;


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
            } elseif ($count == 2) {
                $filter = new FilterStore($table, $filter[0], $filter[1]);
            } else {
                $filter = new FilterStore($table, $filter[0], $filter[2], $filter[1]);
            }
        }
    }
}
