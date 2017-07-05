<?php

namespace OtherCode\Database\Query\Compilers;

/**
 * Class Compiler
 * @package OtherCode\Database\Query\Compilers
 */
abstract class Compiler
{
    /**
     * Allowed operators
     * @var array
     */
    protected $operators = array(
        '=', '<', '>', '<=', '>=', '<>', '!=',
        'like', 'like binary', 'not like', 'between', 'ilike',
        '&', '|', '^', '<<', '>>',
        'rlike', 'regexp', 'not regexp',
        '~', '~*', '!~', '!~*', 'similar to',
        'not similar to'
    );

    /**
     * Default DML blocks to compile
     * @var array
     */
    protected $dmlBlocks = array();

    /**
     * Compile the SQL query
     * @return string
     * @throws \OtherCode\Database\Exceptions\QueryException
     */
    public function compile(\OtherCode\Database\Query $query)
    {
        $sql = array();
        foreach ($this->dmlBlocks as $dmlBlock) {

            if (!method_exists($this, 'compile' . ucfirst($dmlBlock))) {
                throw new \OtherCode\Database\Exceptions\QueryException('Invalid compiler method: compile' . ucfirst($dmlBlock) . '(), the current compiler does not support "' . $dmlBlock . '" statement.');
            }

            $block = $query->get($dmlBlock);
            if (isset($block)) {
                $sql[] = $this->{'compile' . ucfirst($dmlBlock)}($block);
            }
        }

        return trim(implode(" ", $sql));
    }

    /** Compile the SELECT statement
     * @param array $select
     * @return string
     */
    public function compileSelect(array $select)
    {
        return 'SELECT ' . implode(', ', $select);
    }

    /**
     * Compile the UPDATE statement
     * @param array $update
     * @return string
     */
    public function compileUpdate(array $update)
    {
        $blocks = array();
        foreach ($update['values'] as $key => $value) {
            $blocks[] = $key . ' = ' . $value;
        }

        return 'UPDATE ' . $update['table'] . ' SET ' . implode(', ', $blocks);

    }

    /**
     * Compile the DELETE statement
     * @param $delete
     * @return string
     */
    public function compileDelete($delete)
    {
        return "DELETE ";
    }

    /**
     * Compile the INSERT statement
     * @param array $insert
     * @return string
     */
    public function compileInsert(array $insert)
    {
        $block = array();
        $block[] = 'INSERT INTO ' . $insert['table'];

        $fields = array();
        $replaces = array();

        foreach ($insert['values'] as $key => $value) {
            $fields[] = '`' . $key . '`';
            $replaces[] = $value;
        }

        $block[] = '(' . implode(',', $fields) . ')';
        $block[] = (count($insert['values']) > 1) ? 'VALUES' : 'VALUE';
        $block[] = '(' . implode(',', $replaces) . ')';

        return implode(' ', $block);
    }

    /**
     * Compile the FROM statement
     * @param $from
     * @return string
     */
    public function compileFrom($from)
    {
        return 'FROM ' . implode(',', $from);
    }

    /**
     * Compile the WHERE statement
     * @param array $where
     * @return string
     * @throws \OtherCode\Database\Exceptions\QueryException
     */
    public function compileWhere(array $where)
    {
        $block = array();
        foreach ($where as $index => $chunk) {

            if (!is_string($chunk['column'])) {
                throw new \OtherCode\Database\Exceptions\QueryException("The column field is not a string in where clause.");
            }

            if (!is_string($chunk['operator']) || !in_array($chunk['operator'], $this->operators)) {
                throw new \OtherCode\Database\Exceptions\QueryException("Invalid operator in where clause.");
            }

            $sentence = ($index == 0) ? "WHERE " : "AND ";
            $block[] = $sentence . implode(" ", $chunk);
        }

        return implode(' ', $block);
    }

    /**
     * Compile the GROUP statement
     * @param string $group
     * @return string
     */
    public function compileGroup($group)
    {
        return 'GROUP BY ' . $group;
    }

    /**
     * Compile the ORDER statement
     * @param array $order
     * @return string
     * @throws \OtherCode\Database\Exceptions\QueryException
     */
    public function compileOrder(array $order)
    {
        $block = array();
        foreach ($order as $index => $chunk) {

            if (!in_array($chunk[1], array('ASC', 'DESC'))) {
                throw new \OtherCode\Database\Exceptions\QueryException('Invalid order by value, must be ASC or DESC.');
            }

            $block[] = 'ORDER BY ' . implode(" ", $chunk);
        }

        return implode(' ', $block);
    }

    /**
     * Compile the LIMIT statement
     * @param array $limit
     * @return string
     */
    public function compileLimit(array $limit)
    {
        return 'LIMIT ' . implode(" ", $limit);
    }
}