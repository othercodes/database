<?php

namespace OtherCode\Database\Query\Compilers;

/**
 * Class SQLiteCompiler
 * @package OtherCode\Database\Query\Compilers
 */
class SQLiteCompiler extends Compiler
{

    /**
     * Allowed operators
     * @var array
     */
    protected $operators = array(
        '=', '<', '>', '<=', '>=', '<>', '!=',
        'like', 'not like', 'between', 'ilike',
        '&', '|', '<<', '>>',
    );

    /**
     * Default DML blocks to compile
     * @var array
     */
    protected $dmlBlocks = array(
        'select',
        'update',
        'delete',
        'insert',
        'from',
        'where',
        'group',
        'order',
        'limit',
    );
}