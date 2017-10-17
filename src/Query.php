<?php

namespace OtherCode\Database;

/**
 * Class Query
 * @package OtherCode\Database
 */
class Query
{

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
     * Where
     * @var bool
     */
    protected $distinct = false;

    /**
     * In statement
     * @var array
     */
    protected $in = array();

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
     * @param string|array $value
     * @param string $boolean
     * @return $this
     */
    public function where($column, $operator, $value, $boolean = 'and')
    {
        $this->where[] = array(
            'type' => 'basic',
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'boolean' => $boolean,
        );

        return $this;
    }

    /**
     * @param string $column
     * @param string $operator
     * @param string|array $value
     * @return Query
     */
    public function orWhere($column, $operator, $value)
    {
        return $this->where($column, $operator, $value, 'or');
    }

    /**
     * Add a WHERE IN() clause
     * @param $column
     * @param $values
     * @param string $boolean
     * @param bool $not
     * @return $this
     */
    public function whereIn($column, $values, $boolean = 'and', $not = false)
    {
        $this->where[] = array(
            'type' => $not ? 'notIn' : 'in',
            'column' => $column,
            'operator' => null,
            'value' => $values,
            'boolean' => $boolean,
        );

        return $this;
    }

    /**
     * @return $this
     */
    public function distinct()
    {
        $this->distinct = true;
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
     * Return the requested dml block if exist
     * @param string $dmlBlock
     * @return mixed|null
     */
    public function get($dmlBlock)
    {
        if (!is_string($dmlBlock)) {
            throw new \InvalidArgumentException('The input dmlBlock argument must be a string, ' . gettype($dmlBlock) . ' is given.');
        }

        $dmlBlock = trim(strtolower($dmlBlock));

        if (!empty($this->{$dmlBlock})) {
            return $this->{$dmlBlock};
        }

        return null;
    }
}