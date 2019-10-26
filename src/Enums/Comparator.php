<?php

namespace Exper\FilterBuilder\Enums;

class Comparator
{
    const EQ          = '=';
    const GT          = '>';
    const LT          = '<';
    const GT_EQ       = '>=';
    const LT_EQ       = '<=';
    const LIKE        = 'like';
    const BETWEEN     = 'between';
    const NOT_BETWEEN = 'not between';
    const IN          = 'in';
    const NOT_IN      = 'not in';
    const IS_NULL     = 'is null';
    const NOT_NULL    = 'not null';
    const HAS         = 'has';
    const NOT_HAS     = 'not has';

    const ARRAY_VALUE = [
        self::BETWEEN,
        self::NOT_BETWEEN,
        self::IN,
        self::NOT_IN
    ];

    public static function values()
    {
        return [
            self::EQ,
            self::GT,
            self::LT,
            self::GT_EQ,
            self::LT_EQ,
            self::LIKE,
            self::BETWEEN,
            self::NOT_BETWEEN,
            self::IN,
            self::NOT_IN,
            self::IS_NULL,
            self::NOT_NULL,
            self::HAS,
            self::NOT_HAS,
        ];
    }
}
