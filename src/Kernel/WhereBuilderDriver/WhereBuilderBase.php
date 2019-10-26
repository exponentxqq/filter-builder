<?php


namespace Exper\FilterBuilder\Kernel\WhereBuilderDriver;


use Exper\FilterBuilder\FilterStore;
use Exper\FilterBuilder\MapGetters\Transfer;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

abstract class WhereBuilderBase
{
    protected $where;
    protected $field;
    protected $filter;
    protected $relation;

    public function __construct(FilterStore $filterStore, $relation)
    {
        $this->where = $relation == 'and' ? 'where' : 'orWhere';
        $this->field = Transfer::get($filterStore->table, $filterStore->field);
        $this->filter = $filterStore;
        $this->relation = $relation;
    }

    /**
     * @param EloquentBuilder|QueryBuilder $query
     * @return mixed
     */
    abstract public function build($query);
}
