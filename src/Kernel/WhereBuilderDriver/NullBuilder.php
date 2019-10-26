<?php


namespace Exper\FilterBuilder\Kernel\WhereBuilderDriver;


use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class NullBuilder extends WhereBuilderBase
{

    /**
     * @param EloquentBuilder|QueryBuilder $query
     * @return mixed
     */
    public function build($query)
    {
        $where = $this->where;
        $query->$where($this->field);
    }
}
