<?php


namespace Exper\FilterBuilder\Kernel\WhereBuilderDriver;


class ComparatorBuilder extends WhereBuilderBase
{
    public function build($query)
    {
        if ($this->filter->value === null) {
            return;
        }
        $where = $this->where;
        $query->$where($this->field, $this->filter->comparator, $this->filter->value);
    }
}
