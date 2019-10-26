<?php


namespace Exper\FilterBuilder\Kernel\WhereBuilderDriver;


use Exper\FilterBuilder\Enums\Comparator;
use Exper\FilterBuilder\Exceptions\InvalidWhereBuilder;
use Exper\FilterBuilder\Kernel\FilterBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class HasBuilder extends WhereBuilderBase
{

    public function build($query)
    {
        switch ($this->filter->comparator) {
            case Comparator::HAS:
                if (is_array($this->filter->value) &&
                    in_array(array_first($this->filter->value), Comparator::values()) ||
                    is_numeric($this->filter->value)
                ) {
                    $where = $this->relation == 'or' ? 'orHas' : 'has';
                } else {
                    $where = $this->where . 'Has';
                }
                break;
            case Comparator::NOT_HAS:
                if (is_array($this->filter->value) &&
                    in_array(array_first($this->filter->value), Comparator::values()) ||
                    is_numeric($this->filter->value)
                ) {
                    $where = $this->relation == 'or' ? 'orDoesntHave' : 'doesntHave';
                } else {
                    $where = $this->where . 'DoesntHave';
                }
                break;
            default:
                throw new InvalidWhereBuilder();
        }
        $this->handle($query, $where);
    }

    /**
     * @param EloquentBuilder|QueryBuilder $query
     * @param $where
     */
    private function handle($query, $where)
    {
        if (is_array($this->filter->value)) {
            $first = array_first($this->filter->value);
            if (in_array($first, Comparator::values())) {
                $query->$where($this->field, $this->filter->comparator, $this->filter->value);
            } else {
                $query->$where($this->field, function ($builder) {
                    (new FilterBuilder())->build($builder, $this->filter->value);
                });
            }
        } elseif (is_numeric($this->filter->value)) {
            $query->$where($this->field, '>=', $this->filter->value);
        } else {
            $query->$where($this->field);
        }
    }
}
