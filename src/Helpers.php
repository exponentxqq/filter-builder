<?php

namespace Exper\FilterBuilder;

use Exper\FilterBuilder\Enums\Comparator;
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

    /**
     * @param FilterStore $filterStore
     * @param EloquentBuilder|QueryBuilder $query
     * @param $relation
     */
    public static function whereCase(FilterStore $filterStore, $query, $relation)
    {
        $where = $relation == 'and' ? 'where' : 'orWhere';
        $field = Transfer::get($filterStore->table, $filterStore->field);
        switch ($filterStore->comparator) {
            case Comparator::IN:
            case Comparator::NOT_IN:
            case Comparator::BETWEEN:
            case Comparator::NOT_BETWEEN:
                $where = $where . str_replace(' ', '', ucwords($filterStore->comparator));
                $query->$where($field, $filterStore->value);
                break;
            case Comparator::NOT_NULL:
                $where .= 'NotNull';
                $query->$where($field);
                break;
            case Comparator::IS_NULL:
                $where .= 'Null';
                $query->$where($field);
                break;
            case Comparator::HAS:
                $where .= 'Has';
                $query->$where($field);
                break;
            case Comparator::NOT_HAS:
                $where .= 'DoesntHave';
                $query->$where($field);
                break;
            case Comparator::EQ:
            case Comparator::LIKE:
            case Comparator::GT:
            case Comparator::GT_EQ:
            case Comparator::LT:
            case Comparator::LT_EQ:
            default:
                $query->$where($field, $filterStore->comparator, $filterStore->value);
                break;
        }
    }
}

