<?php

namespace Exper\FilterBuilder;

use Exper\FilterBuilder\Enums\Comparator;

class FilterStore
{
    public $table;
    public $field;
    public $comparator;
    public $value;

    public function __construct($table, $field, $value, $comparator = Comparator::EQ)
    {
        $this->field = $field;
        $this->comparator = $comparator;
        if (in_array($this->comparator, Comparator::ARRAY_VALUE)) {
            if (is_array($value)) {
                $this->value = $value;
            } elseif (preg_match('/\[.*\]/', $value)) {
                $this->value = json_decode($value, true);
            } else {
                $this->value = explode(',', $value);
            }
        } elseif ($comparator == Comparator::LIKE) {
            if (false !== strpos($value, '%')) {
                $this->value = $value;
            } else {
                $this->value = "%{$value}%";
            }
        } else {
            $this->value = $value;
        }
    }
}
