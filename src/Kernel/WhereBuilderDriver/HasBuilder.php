<?php


namespace Exper\FilterBuilder\Kernel\WhereBuilderDriver;


use Exper\FilterBuilder\Enums\Comparator;
use Exper\FilterBuilder\Exceptions\InvalidWhereBuilder;
use Exper\FilterBuilder\Helpers;
use Exper\FilterBuilder\Kernel\FilterBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class HasBuilder extends WhereBuilderBase
{

    public function build($query)
    {
        switch ($this->filter->comparator) {
            case Comparator::HAS:
                $where = $this->relation == 'or' ? 'orHas' : 'has';
                break;
            case Comparator::NOT_HAS:
                $where = $this->relation == 'or' ? 'orDoesntHave' : 'doesntHave';
                break;
            default:
                throw new InvalidWhereBuilder();
        }
        $this->handle($query, $where);
    }

    /**
     * @param  EloquentBuilder|QueryBuilder  $query
     * @param $where
     */
    private function handle($query, $where)
    {
        if (is_array($this->filter->value)) {
            $first = array_first($this->filter->value);
            if (in_array($first, Comparator::values())) {
                call_user_func_array(
                    [$query, $where],
                    [Helpers::formatField($this->field, null), $this->filter->comparator, $this->filter->value]
                );
            } else {
                call_user_func_array(
                    [$query, $where],
                    [
                        Helpers::formatField($this->field, null),
                        '>=',
                        1,
                        $this->relation,
                        function ($builder) {
                            (new FilterBuilder())->build($builder, $this->filter->value);
                        }
                    ]
                );
            }
        } elseif (is_numeric($this->filter->value)) {
            call_user_func_array(
                [$query, $where],
                [Helpers::formatField($this->field, null), '>=', $this->filter->value]
            );
        } else {
            call_user_func_array([$query, $where], [Helpers::formatField($this->field, null)]);
        }
    }
}
