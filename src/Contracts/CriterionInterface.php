<?php

namespace Exper\FilterBuilder\Contracts;

use Exper\FilterBuilder\FilterStore;
use Exper\FilterBuilder\Kernel\WhereBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

abstract class CriterionInterface
{
    public function apply(FilterStore $filterStore, $query, $relation = 'and')
    {
        if ($filter = $this->handle($filterStore, $query, $relation)) {
            WhereBuilder::build($filter, $query, $relation);
        }
    }

    /**
     * @param FilterStore $filterStore
     * @param QueryBuilder|EloquentBuilder $query
     * @param string $relation
     */
    abstract protected function handle(FilterStore $filterStore, $query, $relation = 'and');
}
