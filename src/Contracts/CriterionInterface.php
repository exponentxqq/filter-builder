<?php

namespace Exper\FilterBuilder\Contracts;

use Exper\FilterBuilder\FilterStore;

abstract class CriterionInterface
{
    public function apply(FilterStore $filterStore, $query, $relation = 'and')
    {

    }


}
