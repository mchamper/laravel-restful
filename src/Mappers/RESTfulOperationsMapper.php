<?php

namespace App\Packages\v1\RESTful\Mappers;

class RESTfulOperationsMapper
{
    private static $_operations = [
        'eq' => '=',
        '!eq' => '!=',
        '<' => '<',
        '<eq' => '<=',
        '>' => '>',
        '>eq' => '>=',
        'like' => 'like',
        '!like' => 'not like',
    ];

    public static function get(String $operation) {
        return static::$_operations[$operation];
    }
}