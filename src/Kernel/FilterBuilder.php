<?php

namespace Exper\FilterBuilder\Kernel;

use Exper\FilterBuilder\Exceptions\InvalidFiltersException;
use Exper\FilterBuilder\Contracts\CriterionInterface;
use Exper\FilterBuilder\FilterStore;
use Exper\FilterBuilder\Helpers;
use Exper\FilterBuilder\src\Exceptions\InvalidCriterionException;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

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

    /** @var CriterionInterface[] $criteria */
    private $criteria;

    /**
     * @param EloquentBuilder|QueryBuilder $query
     * @param array $filters
     * @return EloquentBuilder|QueryBuilder
     */
    public function build($query, $filters)
    {
        return $this->setQuery($query)->setFilters($filters)->buildQuery($query, $this->filters);
    }

    public function setFilters(array $filters)
    {
        $this->filters = $this->formatFilters($filters);
        return $this;
    }

    /**
     * @param EloquentBuilder|QueryBuilder $query
     * @return $this
     */
    public function setQuery($query)
    {
        if (!$query instanceof EloquentBuilder && !$query instanceof QueryBuilder) {
            throw new \InvalidArgumentException(
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
        $this->query = $query;
        return $this;
    }

    /**
     * 格式化filter参数，当filter有对应的Criterion类时，将获取该类
     *
     * @param $filters
     *
     * @return mixed
     */
    private function formatFilters($filters)
    {
        if (count($filters) > 0) {
            if (is_string(array_first(array_keys($filters))) && strlen(array_first(array_keys($filters)))) {
                if (is_array(array_first($filters))) {
                    foreach ($filters as $key => &$filter) {
                        if (is_string($key) && strlen($key)) {
                            if (is_array($filter)) {
                                $items = [];
                                if (is_array(array_first($filter))) {
                                    foreach ($filter as $item) {
                                        if (is_array($item)) {
                                            if (is_array(array_first($item))) {
                                                $item = $this->formatFilters([$key => $item]);
                                            } else {
                                                $item = new FilterStore($this->table, $key, $item[1], $item[0]);
                                            }
                                        } elseif ($item !== 'or' && $item !== 'and') {
                                            $item = new FilterStore($this->table, $key, $item[1], $item[0]);
                                        }
                                        if (is_array($item)) {
                                            $items = array_merge($items, $item);
                                        } else {
                                            $items[] = $item;
                                        }
                                    }
                                    $filter = $items;
                                } else {
                                    $filter = new FilterStore($this->table, $key, $filter[1], $filter[0]);
                                }
                            } elseif ($filter !== 'or' && $filter !== 'and') {
                                $filter = new FilterStore($this->table, $key, $filter);
                            }
                        } elseif (is_array($filter)) {
                            if (is_array(array_first($filter))) {
                                $filter = $this->formatFilters($filter);
                            } else {
                                if (count($filter) == 2) {
                                    $filter = new FilterStore($this->table, $filter[0], $filter[1]);
                                } else {
                                    $filter = new FilterStore($this->table, $filter[0], $filter[2], $filter[1]);
                                }
                            }
                        }
                    }
                } elseif (array_first($filters) !== 'or' && array_first($filters) !== 'and') {
                    $filters = new FilterStore($this->table, array_first(array_keys($filters)), array_first($filters));
                }
            } else {
                if (is_array(array_first($filters))) {
                    foreach ($filters as $key => &$filter) {
                        if (is_string($key) && strlen($key)) {
                            if (is_array($filter)) {
                                $items = [];
                                if (is_array(array_first($filter))) {
                                    foreach ($filter as $item) {
                                        if (is_array($item)) {
                                            if (is_array(array_first($item))) {
                                                $item = $this->formatFilters([$key => $item]);
                                            } else {
                                                $item = new FilterStore($this->table, $key, $item[1], $item[0]);
                                            }
                                        } elseif ($item !== 'or' && $item !== 'and') {
                                            $item = new FilterStore($this->table, $key, $item[1], $item[0]);
                                        }
                                        if (is_array($item)) {
                                            $items = array_merge($items, $item);
                                        } else {
                                            $items[] = $item;
                                        }
                                    }
                                    $filter = $items;
                                } else {
                                    $filter = new FilterStore($this->table, $key, $filter[1], $filter[0]);
                                }
                            } elseif ($filter !== 'or' && $filter !== 'and') {
                                $filter = new FilterStore($this->table, $key, $filter);
                            }
                        } elseif (is_array($filter)) {
                            if (is_array(array_first($filter))) {
                                $filter = $this->formatFilters($filter);
                            } else {
                                if (count($filter) == 2) {
                                    $filter = new FilterStore($this->table, $filter[0], $filter[1]);
                                } else {
                                    $filter = new FilterStore($this->table, $filter[0], $filter[2], $filter[1]);
                                }
                            }
                        }
                    }
                } elseif (array_first($filters) !== 'or' && array_first($filters) !== 'and') {
                    $filters = new FilterStore($this->table, array_first(array_keys($filters)), array_first($filters));
                }
            }
        } else {
            throw new InvalidFiltersException();
        }

        return $filters;
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
        if (isset($this->criteria[$filterStore->field])) {
            if (is_string($this->criteria[$filterStore->field])) {
                return new $this->criteria[$filterStore->field]();
            } else {
                return $this->criteria[$filterStore->field];
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
        $criterion = $this->makeCriterion($filterStore);
        if ($criterion) {
            $criterion->apply($filterStore, $query, $relation);
        } else {
            Joiner::checkAndJoin($filterStore, $this->query);
            Helpers::whereCase($filterStore, $query, $relation);
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
            $this->criteria[$field] = $criterion;
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
