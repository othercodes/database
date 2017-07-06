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
        '&', '|', '#', '<<', '>>', 'in',
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
     * @param array $insert
     * @return string
     */
    public function compileInsert(array $insert)
    {
        $block = array();
        $block[] = 'INSERT INTO ' . $insert['table'];
        $block[] = '(' . implode(', ', array_keys($insert['values'])) . ')';
        $block[] = 'VALUES';
        $block[] = '(' . implode(', ', array_values($insert['values'])) . ')';

        return implode(' ', $block);
    }
}