<?php

namespace OtherCode\Database\Query\Compilers;

/**
 * Class MySQLCompiler
 * @package OtherCode\Database\Query\Compilers
 */
class MySQLCompiler extends Compiler
{

    /**
     * Allowed operators
     * @var array
     */
    protected $operators = array(
        '=', '<', '>', '<=', '>=', '<>', '!=',
        'like', 'not like', 'between', 'not',
        '&', '|', '<<', '>>', '%', 'mod', 'is not',
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
        'replace',
        'from',
        'where',
        'group',
        'order',
        'limit',
    );

    public function compileReplace($replace)
    {
        $block = array();

        $block[] = 'REPLACE INTO';
        $block[] = $replace['table'];
        $fields = array();
        $replaces = array();
        foreach ($replace['values'] as $key => $value) {
            $fields[] = '`' . $key . '`';
            $replaces[] = $value;
        }
        $block[] = '(' . implode(',', $fields) . ')';
        $block[] = (count($replace['values']) > 1) ? 'VALUES' : 'VALUE';
        $block[] = '(' . implode(',', $replaces) . ')';

        return implode(' ', $block);
    }
}