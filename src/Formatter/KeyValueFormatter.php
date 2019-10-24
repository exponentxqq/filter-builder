<?php


namespace Exper\FilterBuilder\src\Formatter;


use Exper\FilterBuilder\FilterStore;

class KeyValueFormatter
{
    public function __construct($field, $filters)
    {

    }

    public static function format($field, $filters)
    {
        if (is_array($filters)) {
            if (is_array(array_first($filters))) {
                $filterItems = [];
                foreach ($filters as $k => $filter) {
                    if (!is_string($filter)) {
                        $filterItems[$k] = new FilterStore($key, $filter[1], $filter[0]);
                    } else {
                        $filterItems[$k] = $filter;
                    }
                }
                $filter = $filterItems;
            } else {
                $filter = new FilterStore($key, $filter[1], $filter[0]);
            }
        } else {
            $filter = new FilterStore($key, $filter);
        }
        return $filters;
    }
}
