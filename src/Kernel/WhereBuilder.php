<?php


namespace Exper\FilterBuilder\Kernel;


use Exper\FilterBuilder\Enums\Comparator;
use Exper\FilterBuilder\FilterStore;
use Exper\FilterBuilder\Kernel\WhereBuilderDriver\ArrayValueBuilder;
use Exper\FilterBuilder\Kernel\WhereBuilderDriver\ComparatorBuilder;
use Exper\FilterBuilder\Kernel\WhereBuilderDriver\HasBuilder;
use Exper\FilterBuilder\Kernel\WhereBuilderDriver\NullBuilder;

class WhereBuilder
{
    public static function build(FilterStore $filterStore, $query, $relation)
    {
        switch ($filterStore->comparator) {
            case Comparator::IN:
            case Comparator::NOT_IN:
            case Comparator::BETWEEN:
            case Comparator::NOT_BETWEEN:
                (new ArrayValueBuilder($filterStore, $relation))->build($query);
                break;
            case Comparator::NOT_NULL:
            case Comparator::IS_NULL:
                (new NullBuilder($filterStore, $relation))->build($query);
                break;
            case Comparator::HAS:
            case Comparator::NOT_HAS:
                (new HasBuilder($filterStore, $relation))->build($query);
                break;
            case Comparator::EQ:
            case Comparator::LIKE:
            case Comparator::GT:
            case Comparator::GT_EQ:
            case Comparator::LT:
            case Comparator::LT_EQ:
            default:
                (new ComparatorBuilder($filterStore, $relation))->build($query);
                break;
        }
    }
}
