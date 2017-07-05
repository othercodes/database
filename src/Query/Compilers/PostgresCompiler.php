<?php

namespace OtherCode\Database\Query\Compilers;

/**
 * Class PostgresCompiler
 * @package OtherCode\Database\Query\Compilers
 */
class PostgresCompiler extends Compiler
{

    /**
     * Allowed operators
     * @var array
     */
    protected $operators = array(
        '=', '<', '>', '<=', '>=', '<>', '!=',
        'like', 'not like', 'between', 'ilike',
        '&', '|', '#', '<<', '>>',
        '@>', '<@', '?', '?|', '?&', '||', '-', '-', '#-',
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

    /**
     * Compile the INSERT statement
     * @param $insert
     * @return string
     */
    public function compileInsert($insert)
    {
        $block = array();
        $block[] = 'INSERT INTO ' . $insert['table'];
        $block[] = '(' . implode(', ', array_keys($insert['values'])) . ')';
        $block[] = 'VALUES';
        $block[] = '(' . implode(', ', array_values($insert['values'])) . ')';

        return implode(' ', $block);
    }
}