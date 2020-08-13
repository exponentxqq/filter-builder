<?php

namespace Exper\FilterBuilder\Kernel;

use Exper\FilterBuilder\FilterStore;
use Exper\FilterBuilder\Formatter\FilterFormatter;

class FilterHandler
{
    private $filters;

    private $reducedFilters;

    public function __construct(array $filters, ?string $mainTable)
    {
        $this->filters = FilterFormatter::format($mainTable ?? '', $filters);
        $this->reducedFilters = $this->reduceFilters(FilterFormatter::format($mainTable ?? '', $filters));
    }

    public function get(string $field): ?FilterStore
    {
        return $this->exists($field) ? $this->reducedFilters[$field] : null;
    }

    public function exists(string $field): bool
    {
        return key_exists($field, $this->reducedFilters);
    }

    public function getValue(string $field, $default = null)
    {
        return optional($this->get($field))->value ?? $default;
    }

    public function getFilters()
    {
        return $this->filters;
    }

    public function getReducedFilters()
    {
        return $this->reducedFilters;
    }

    private function reduceFilters($filters)
    {
        static $reducedFilters = [];
        if (is_array($filters)) {
            foreach ($filters as $filter) {
                if (is_array($filter)) {
                    self::reduceFilters($filter);
                } else {
                    if ($filter instanceof FilterStore) {
                        $reducedFilters[$filter->field] = $filter;
                    }
                }
            }
        } else {
            $reducedFilters[$filters->field] = $filters;
        }
        return $reducedFilters;
    }
}