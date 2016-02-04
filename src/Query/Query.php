<?php

namespace OtherCode\Database\Query;

use InvalidArgumentException;

class Query
{
    protected $statements = array(
        'SELECT',
        'INSERT',
        'UPDATE',
        'DELETE',
        'FROM',
        'WHERE'
    );

    protected $operators = array(
        '=', '<', '>', '<=', '>=', '<>', '!=',
        'like', 'like binary', 'not like', 'between', 'ilike',
        '&', '|', '^', '<<', '>>',
        'rlike', 'regexp', 'not regexp',
        '~', '~*', '!~', '!~*', 'similar to',
        'not similar to'
    );

    private $select;

    private $update;

    private $delete;

    private $from;

    private $where;

    private $groups;

    private $orders;

    private $limit;

    public function select(Array $columns = array('*'))
    {
        $this->select = $columns;
        return $this;
    }

    public function delete($id = null)
    {
        $this->delete = $id;
        return $this;
    }

    public function update(array $values)
    {
        $this->update = $values;
        return $this;
    }

    public function from(Array $tables)
    {
        $this->from = $tables;
        return $this;
    }

    public function where($column, $operator, $value)
    {
        $this->where[] = array(
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
        );
        return $this;
    }

    private function compileQuery()
    {
        $sql = array();

        if(count($this->select) > 0){
            $sql[] = "SELECT " . implode(',',$this->select);
        }

        if(count($this->update) > 0)
        {
            $sql[] = "UPDATE " . implode(",",$this->update);
        }

        if($this->delete){
            $sql[] = "DELETE ";
        }

        if(count($this->select) > 0) {
            $sql[] = "FROM " . implode(',', $this->from);
        }

        foreach ($this->where as $index => $where) {

            if(!is_string($where['column'])){
                throw new InvalidArgumentException("The column field is not a string.");
            }

            if(!is_string($where['operator']) || !in_array($where['operator'],$this->operators)){
                throw new InvalidArgumentException("Invalid operator.");
            }

            if (gettype($where['value']) == 'string') {
                $where['value'] = '"' . $where['value'] . '"';
            }

            $sentence = ($index == 0) ? "WHERE " : "AND ";
            $sql[] = $sentence . implode(" ",$where);

        }



        return $sql;
    }

    public function __toString()
    {
        return implode(" ", $this->compileQuery());
    }
}