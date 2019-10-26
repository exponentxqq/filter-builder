<?php

namespace Exper\FilterBuilder\Formatter;

use Exper\FilterBuilder\Enums\Comparator;
use Exper\FilterBuilder\Exceptions\InvalidFiltersException;
use Exper\FilterBuilder\FilterStore;

class KeyValueFormatter
{
    /**
     * format follow example
     *
     * field => 1
     * field => [>, 1]
     * field => [[>, 1], [<, 4], or]
     * field => [in, [1, 2, 3]]
     * field => [[[>, 1], [<, 3]], [[>, 6], [<, 10]], or]
     *
     * @param $table
     * @param $field
     * @param $filters
     */
    public static function format($table, $field, &$filters)
    {
        if (is_array($filters)) {
            if (is_array(array_first($filters))) {
                foreach ($filters as &$filter) {
                    if ($filter !== 'or' && $filter !== 'and') {
                        if (is_array(array_first($filter))) {
                            static::format($table, $field, $filter);
                        } else {
                            if (!in_array($filter[0], Comparator::values())) {
                                throw new InvalidFiltersException("invalid filter: {$table} => {$filter[0]}");
                            }
                            $filter = new FilterStore($table, $field, $filter[1] ?? null, $filter[0]);
                        }
                    }
                }
            } else {
                if (!in_array($filters[0], Comparator::values())) {
                    throw new InvalidFiltersException("invalid filter: {$table} => {$filters[0]}");
                }
                $filters = new FilterStore($table, $field, $filters[1] ?? null, $filters[0]);
            }
        } else {
            if (in_array($filters, Comparator::values())) {
                $filters = new FilterStore($table, $field, null, $filters);
            } else {
                $filters = new FilterStore($table, $field, $filters);
            }
        }
    }
}
