<?php

namespace Exper\FilterBuilder\Enums;

class Comparator
{
    const EQ = '=';
    const GT = '>';
    const LT = '<';
    const GT_EQ = '>=';
    const LT_EQ = '<=';
    const LIKE = 'like';
    const BETWEEN = 'between';
    const NOT_BETWEEN = 'not between';
    const IN = 'in';
    const NOT_IN = 'not in';
    const IS_NULL = 'is null';
    const NOT_NULL = 'not null';

    const ARRAY_VALUE = [
        self::BETWEEN,
        self::NOT_BETWEEN,
        self::IN,
        self::NOT_IN
    ];
}
