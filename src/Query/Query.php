<?php

namespace OtherCode\Database\Query;

/**
 * Class Query
 * @package OtherCode\Database\Query
 */
class Query
{
    /**
     * @var array
     */
    protected $statements = array(
        'SELECT',
        'INSERT',
        'UPDATE',
        'DELETE',
        'FROM',
        'WHERE'
    );

    /**
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
     * @var
     */
    private $select;

    /**
     * @var
     */
    private $update;

    /**
     * @var
     */
    private $delete;

    /**
     * @var
     */
    private $from;

    /**
     * @var
     */
    private $where;

    /**
     * @var
     */
    private $groups;

    /**
     * @var
     */
    private $orders;

    /**
     * @var
     */
    private $limit;

    /**
     * Add a select clause
     * @param array $columns
     * @return $this
     */
    public function select(Array $columns = array('*'))
    {
        $this->select = $columns;
        return $this;
    }

    /**
     * Add a delete clause
     * @param null $id
     * @return $this
     */
    public function delete($id = null)
    {
        $this->delete = $id;
        return $this;
    }

    /**
     * Add an update clause
     * @param array $values
     * @return $this
     */
    public function update(array $values)
    {
        $this->update = $values;
        return $this;
    }

    /**
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
     * Add a where clause
     * @param $column
     * @param $operator
     * @param $value
     * @return $this
     */
    public function where($column, $operator, $value)
    {
        $this->where[] = array(
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
        );
        return $this;
    }

    /**
     * Compile each clause of the query to SQL
     * @return string
     * @throws \OtherCode\Database\Exceptions\QueryException
     */
    public function compile()
    {
        $sql = array();

        if (count($this->select) > 0) {
            $sql[] = "SELECT " . implode(',', $this->select);
        }

        if (count($this->update) > 0) {
            $sql[] = "UPDATE " . implode(",", $this->update);
        }

        if ($this->delete) {
            $sql[] = "DELETE ";
        }

        if (count($this->select) > 0) {
            $sql[] = "FROM " . implode(',', $this->from);
        }

        foreach ($this->where as $index => $where) {

            if (!is_string($where['column'])) {
                throw new \OtherCode\Database\Exceptions\QueryException("The column field is not a string in where clause.");
            }

            if (!is_string($where['operator']) || !in_array($where['operator'], $this->operators)) {
                throw new \OtherCode\Database\Exceptions\QueryException("Invalid operator in where clause.");
            }

            if (gettype($where['value']) == 'string') {
                $where['value'] = '"' . $where['value'] . '"';
            }

            $sentence = ($index == 0) ? "WHERE " : "AND ";
            $sql[] = $sentence . implode(" ", $where);

        }

        return implode(" ", $sql);
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