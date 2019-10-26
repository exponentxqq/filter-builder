<?php


namespace Exper\FilterBuilder\Kernel\WhereBuilderDriver;


use Exper\FilterBuilder\FilterStore;

class ArrayValueBuilder extends WhereBuilderBase
{
    public function build($query)
    {
        if ($this->filter->value === null) {
            return;
        }
        $where = $this->where . str_replace(' ', '', ucwords($this->filter->comparator));
        $query->$where($this->field, $this->filter->value);
    }
}
