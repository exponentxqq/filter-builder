<?php

namespace Exper\FilterBuilder;

use Exper\FilterBuilder\Enums\Comparator;

class FilterStore
{
    public $table;
    public $fieldTable;
    public $field;
    public $comparator;
    public $value;

    public function __construct($table, $field, $value, $comparator = Comparator::EQ)
    {
        $this->table = $table;
        $fieldInfo = explode('.', $field);
        $this->fieldTable = count($fieldInfo) > 1 ? $fieldInfo[0] : $table;
        $this->field = $field;
        $this->comparator = $comparator;
        $this->setValue($value);
    }

    public function setValue($value)
    {
        if (in_array($this->comparator, Comparator::ARRAY_VALUE)) {
            if (is_array($value)) {
                $this->value = $value;
            } elseif (preg_match('/\[.*\]/', $value)) {
                $this->value = json_decode($value, true);
            } else {
                $this->value = explode(',', $value);
            }
        } elseif ($this->comparator == Comparator::LIKE) {
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
