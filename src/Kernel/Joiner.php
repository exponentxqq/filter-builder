<?php

namespace Exper\FilterBuilder\Kernel;

use Exper\FilterBuilder\FilterStore;
use Exper\FilterBuilder\MapGetters\Relation;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Query\JoinClause;

class Joiner
{
    /**
     * @param EloquentBuilder|QueryBuilder $query
     * @param string $table
     * @return bool
     */
    public static function isJoin($query, $table)
    {
        $joinQuery = $query;
        $joins = null;
        while (!$joins && $joinQuery) {
            if (property_exists($joinQuery, 'joins')) {
                $joins = $joinQuery->joins;
                break;
            }
            $joinQuery = $joinQuery->getQuery();
        }
        $joins = collect($joins)->pluck('table')->toArray();
        return in_array($table, $joins ?? []);
    }

    /**
     * @param FilterStore $filterStore
     * @param EloquentBuilder|QueryBuilder $query
     */
    public static function checkAndJoin(FilterStore $filterStore, $query)
    {
        if ($filterStore->table !== $filterStore->fieldTable && !static::isJoin($query, $filterStore->fieldTable)) {
            if ($relations = Relation::get($filterStore->table, $filterStore->fieldTable)) {
                !static::isJoin($query, $filterStore->fieldTable)
                and static::join($query, $filterStore->fieldTable, $relations);
            }
        }
    }

    /**
     * @param QueryBuilder|EloquentBuilder $query
     * @param string $table
     * @param array $relations
     */
    public static function join($query, $table, $relations)
    {
        if (is_array(array_first($relations))) {
            $isOrOn = false;
            if (!is_array($last = array_last($relations))) {
                $isOrOn = $last == 'or' ? true : false;
                array_pop($relations);
            }
            $query->leftJoin($table, function (JoinClause $join) use ($relations, $isOrOn) {
                foreach ($relations as $relation) {
                    if ($isOrOn) {
                        $join->orOn($relation[0], '=', $relation[1]);
                    } else {
                        $join->on($relation[0], '=', $relation[1]);
                    }
                }
            });
        } else {
            $query->leftJoin($table, $relations[0], '=', $relations[1]);
        }
    }
}
