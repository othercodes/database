<?php

namespace OtherCode\Database;

/**
 * Class Query
 * @package OtherCode\Database
 */
class Query
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
    protected $dmlBlock = array(
        'DELETE',
        'INSERT',
        'REPLACE',
        'SELECT',
        'UPDATE',
    );

    /**
     * Select values
     * @var array
     */
    protected $select;

    /**
     * Update values
     * @var array
     */
    protected $update;

    /**
     * Delete values
     * @var array
     */
    protected $delete;

    /**
     * Insert values
     * @var array
     */
    protected $insert;

    /**
     * From values
     * @var array
     */
    protected $from;

    /**
     * Where conditions
     * @var array
     */
    protected $where = array();

    /**
     * Order by values
     * @var array
     */
    protected $order = array();

    /**
     * Groups values
     * @var array
     */
    protected $group;

    /**
     * Limit values
     * @var array
     */
    protected $limit;

    /**
     * Compiler system for the query
     * @var \OtherCode\Database\Query\Compilers\Compiler
     */
    public $compiler;

    /**
     * Add SELECT clause
     * @param array $columns
     * @return $this
     */
    public function select(array $columns = array('*'))
    {
        $this->select = $columns;
        return $this;
    }

    /**
     * Add UPDATE clause
     * @param string $table
     * @param array $columns
     * @return $this
     */
    public function update($table, array $columns)
    {
        $this->update = array(
            'table' => $table,
            'values' => $columns
        );
        return $this;
    }

    /**
     * Add a delete clause
     * @param array $id
     * @return $this
     */
    public function delete(array $id)
    {
        $this->delete = $id;
        return $this;
    }

    /**
     * Add INSERT clause
     * @param string $table
     * @param array $columns
     * @return $this
     */
    public function insert($table, array $columns)
    {
        $this->insert = array(
            'table' => $table,
            'values' => $columns
        );
        return $this;
    }

    /**
     * Add REPLACE INTO clause
     * @param string $table
     * @param array $columns
     * @return $this
     */
    public function replace($table, array $columns)
    {
        $this->replace = array(
            'table' => $table,
            'values' => $columns
        );
        return $this;
    }

    /**
     * Add FROM clause
     * @param array|string $tables
     * @return $this
     */
    public function from($tables)
    {
        if (is_array($tables)) {
            $this->from = $tables;
        } else {
            $this->from = array($tables);
        }
        return $this;
    }

    /**
     * Add a new WHERE/AND clause
     * @param string $column
     * @param string $operator
     * @param string $value
     * @param boolean $quoted
     * @return $this
     */
    public function where($column, $operator, $value, $quoted = false)
    {
        $this->where[] = array(
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'quoted' => $quoted
        );
        return $this;
    }

    /**
     * Add GROUP BY clause
     * @param string $field
     * @return $this
     */
    public function groupBy($field)
    {
        $this->group = $field;
        return $this;
    }

    /**
     * Add ORDER BY clause
     * @param string $field
     * @param string $order
     * @return $this
     */
    public function orderBy($field, $order = 'DESC')
    {
        $this->order[] = array($field, $order);
        return $this;
    }

    /**
     * Set the LIMIT clause
     * @param string $limit
     * @param null $offset
     * @return $this
     */
    public function limit($limit = '1000', $offset = null)
    {
        $this->limit = array($offset, $limit);
        return $this;
    }

    /**
     * Compile the SQL query
     * @return string
     * @throws \OtherCode\Database\Exceptions\QueryException
     */
    public function compile()
    {
        $sql = array();

        if (isset($this->select)) {
            $sql[] = 'SELECT ' . implode(',', $this->select);
        }

        if (isset($this->update)) {
            $sql[] = 'UPDATE';
            $sql[] = $this->update['table'];
            $sql[] = 'SET';

            $blocks = array();
            foreach ($this->update['values'] as $key => $value) {
                $blocks[] = $key . ' = ' . $value;
            }

            $sql[] = implode(',', $blocks);
        }

        if (isset($this->delete)) {
            $sql[] = "DELETE ";
        }

        if (isset($this->insert)) {
            $sql[] = 'INSERT INTO';
            $sql[] = $this->insert['table'];

            $fields = array();
            $replaces = array();

            foreach ($this->insert['values'] as $key => $value) {
                $fields[] = '`' . $key . '`';
                $replaces[] = $value;
            }

            $sql[] = '(' . implode(',', $fields) . ')';
            $sql[] = (count($this->insert['values']) > 1) ? 'VALUES' : 'VALUE';
            $sql[] = '(' . implode(',', $replaces) . ')';
        }

        if (isset($this->replace)) {
            $sql[] = 'REPLACE INTO';
            $sql[] = $this->replace['table'];

            $fields = array();
            $replaces = array();

            foreach ($this->replace['values'] as $key => $value) {
                $fields[] = '`' . $key . '`';
                $replaces[] = $value;
            }

            $sql[] = '(' . implode(',', $fields) . ')';
            $sql[] = (count($this->replace['values']) > 1) ? 'VALUES' : 'VALUE';
            $sql[] = '(' . implode(',', $replaces) . ')';
        }

        if (isset($this->from)) {
            $sql[] = 'FROM ' . implode(',', $this->from);
        }

        foreach ($this->where as $index => $where) {

            if (!is_string($where['column'])) {
                throw new \OtherCode\Database\Exceptions\QueryException("The column field is not a string in where clause.");
            }

            if (!is_string($where['operator']) || !in_array($where['operator'], $this->operators)) {
                throw new \OtherCode\Database\Exceptions\QueryException("Invalid operator in where clause.");
            }

            if (gettype($where['value']) == 'string' && $where['quoted'] === true) {
                $where['value'] = '"' . $where['value'] . '"';
            }

            unset($where['quoted']);

            $sentence = ($index == 0) ? "WHERE " : "AND ";
            $sql[] = $sentence . implode(" ", $where);

        }

        if (isset($this->group)) {
            $sql[] = 'GROUP BY ' . $this->group;
        }

        foreach ($this->order as $index => $order) {

            if (!in_array($order[1], array('ASC', 'DESC'))) {
                throw new \OtherCode\Database\Exceptions\QueryException('Invalid order by value, must be ASC or DESC.');
            }

            $sql[] = 'ORDER BY ' . implode(" ", $order);
        }

        if (isset($this->limit)) {
            $sql[] = 'LIMIT ' . implode(" ", $this->limit);
        }

        return trim(implode(" ", $sql));
    }

    /**
     * Add quotes to text
     * @param string|array $text
     * @param bool $escape
     * @return array|string
     */
    public function quote($text, $escape = false)
    {
        if (is_array($text)) {
            foreach ($text as $key => $value) {
                $text[$key] = $this->quote($value, $escape);
            }

            return $text;
        }

        return '\'' . ($escape ? $this->escape($text) : $text) . '\'';
    }

    /**
     * Escape string
     * @param mixed $text
     * @return string
     */
    public function escape($text)
    {
        if (is_int($text) || is_float($text)) {
            return $text;
        }

        return addcslashes(str_replace("'", "''", $text), "\000\n\r\\\032");
    }

    /**
     * Return the final sql string
     * @return string
     */
    public function __toString()
    {
        try {
            return $this->compile();

        } catch (\Exception $e) {

            return $e->getMessage();
        }
    }
}