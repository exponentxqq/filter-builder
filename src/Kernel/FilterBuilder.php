<?php

namespace Exper\FilterBuilder\Kernel;

use Exper\FilterBuilder\Contracts\CriterionInterface;
use Exper\FilterBuilder\Exceptions\InvalidCriterionException;
use Exper\FilterBuilder\Exceptions\QueryNotSetException;
use Exper\FilterBuilder\FilterStore;
use Exper\FilterBuilder\Formatter\FilterFormatter;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use InvalidArgumentException;

/**
 * 根据传递的filters生成相应的laravel查询构造器
 *
 *  支持如下格式
 *  ['id', 1]
 *  ['id', '=', 1]
 *  ['id', 'in', [1, 2]]
 *  ['id' => 1, 'name' => 'aaa']
 *  ['id' => ['>', 1], 'name' => ['like', 'aa']]
 *  ['id' => [['>', 1], ['<', 3], 'or']]
 *  ['id' => 1, ['name', 'like', 'aa']]
 *  [['id' => 1], ['id' => 2], 'or'] // 以数组最后一个元素作为条件分组的类型【and，or】
 */
class FilterBuilder
{
    private $query;

    private $filters;

    private $table;

    /**@var array|string[] $excludes */
    private $excludes = [];

    /** @var CriterionInterface[] $criteria */
    private static $criteria;

    /**
     * @param EloquentBuilder|QueryBuilder $query
     * @param array $filters
     * @return EloquentBuilder|QueryBuilder
     */
    public function build($query, $filters)
    {
        $this->setQuery($query)->setFilters($filters);
        return $this->query->where(function ($builder) {
            $this->buildQuery($builder, $this->filters);
        });
    }

    private function setFilters(array $filters)
    {
        if (!$this->table) {
            throw new QueryNotSetException();
        }
        $this->filters = FilterFormatter::format($this->table, $filters);
        return $this;
    }

    /**
     * @param EloquentBuilder|QueryBuilder $query
     * @return $this
     */
    private function setQuery($query)
    {
        $this->query = $query;
        if (!$query instanceof EloquentBuilder && !$query instanceof QueryBuilder) {
            throw new InvalidArgumentException(
                "query must instanceof " . EloquentBuilder::class . ' or ' . QueryBuilder::class
            );
        }
        $from = '';
        while (!$from && $query) {
            if (isset($query->from)) {
                $from = $query->from;
                break;
            }
            $query = $query->getQuery();
        }
        $this->table = $from;
        return $this;
    }

    /**
     * @param EloquentBuilder|QueryBuilder $query
     * @param array|FilterStore $filters
     *
     * @return EloquentBuilder|QueryBuilder
     */
    private function buildQuery($query, $filters)
    {
        if (is_array($filters)) {
            $last = array_last($filters);
            if ($last == 'and' || $last == 'or') {
                $where = $last == 'and' ? 'where' : 'orWhere';
                $relation = $last;
                array_pop($filters);
            } else {
                $where = 'where';
                $relation = 'and';
            }
            foreach ($filters as $filter) {
                if (is_array($filter)) {
                    $query->$where(function ($builder) use ($filter) {
                        $this->buildQuery($builder, $filter);
                    });
                } else {
                    if ($filter instanceof FilterStore) {
                        $this->buildWhere($filter, $query, $relation);
                    }
                }
            }
        } else {
            $this->buildWhere($filters, $query);
        }
        return $query;
    }

    private function makeCriterion(FilterStore $filterStore): ?CriterionInterface
    {
        if (isset(static::$criteria[$filterStore->field])) {
            if (is_string(static::$criteria[$filterStore->field])) {
                return new static::$criteria[$filterStore->field]();
            } else {
                return static::$criteria[$filterStore->field];
            }
        }
        return null;
    }

    /**
     * @param FilterStore $filterStore
     * @param EloquentBuilder|QueryBuilder $query
     * @param string $relation
     */
    private function buildWhere(FilterStore $filterStore, $query, $relation = 'and')
    {
        if (!in_array($filterStore->field, $this->excludes)) {
            $criterion = $this->makeCriterion($filterStore);
            if ($criterion) {
                $criterion->apply($filterStore, $query, $relation);
            } else {
                Joiner::checkAndJoin($filterStore, $this->query);
                WhereBuilder::build($filterStore, $query, $relation);
            }
        }
    }

    /**
     * @param CriterionInterface $criterion
     * @param string $field
     * @return $this
     */
    public function pushCriterion(CriterionInterface $criterion, string $field)
    {
        if (is_numeric($field) || !is_string($field)) {
            throw new InvalidCriterionException("field must be specified");
        } elseif (!$criterion instanceof CriterionInterface && !class_exists($criterion)) {
            throw new InvalidCriterionException("Criterion [{$criterion}] not exists");
        } else {
            static::$criteria[$field] = $criterion;
        }
        return $this;
    }

    /**
     * @param array|CriterionInterface[] $criteria
     *
     * @return $this
     */
    public function mergeCriterion(array $criteria)
    {
        foreach ($criteria as $field => $criterion) {
            $this->pushCriterion($criterion, $field);
        }
        return $this;
    }
}
