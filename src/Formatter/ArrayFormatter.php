<?php

namespace Exper\FilterBuilder\Formatter;

class ArrayFormatter
{
    /**
     * [field, 1]
     * [[field, >, 1], [field, <, 3], or]
     * @param $table
     * @param $filters
     * @return mixed
     */
    public static function format($table, &$filters)
    {
        foreach ($filters as $key => &$filter) {
            if ($filter !== 'or' && $filter !== 'and') {
                if (is_string($key) && !is_numeric($key)) {
                    KeyValueFormatter::format($table, $key, $filter);
                } elseif (is_array($filter)) {
                    if (array_intersect(['field', 'comparator', 'value'], array_keys($filter))) {
                        $filter = array_values($filter);
                    }
                    ArrayFormatter::format($table, $filter);
                } else {
                    NormalFormatter::format($table, $filters);
                    break;
                }
            }
        }
    }
}
