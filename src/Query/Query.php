<?php

namespace OtherCode\Database\Query;

use InvalidArgumentException;

class Query
{
    protected $statements = array(
        'SELECT',
        'INSERT',
        'UPDATE',
        'FROM',
        'SELECT'
    );

    protected $operators = array(
        '=', '<', '>', '<=', '>=', '<>', '!=',
        'like', 'like binary', 'not like', 'between', 'ilike',
        '&', '|', '^', '<<', '>>',
        'rlike', 'regexp', 'not regexp',
        '~', '~*', '!~', '!~*', 'similar to',
        'not similar to'
    );

    private $select = array();

    private $update = array();

    private $updateValues = array();

    private $delete = false;

    private $from = array();

    private $where = array();

    public function select(Array $columns = array('*'))
    {
        $this->select = $columns;
    }

    public function delete()
    {
        $this->delete = true;
    }

    public function update(Array $tables)
    {
        $this->update = $tables;
    }

    public function setValues(Array $values)
    {
        $this->updateValues = $values;
    }

    public function from(Array $tables)
    {
        $this->from = $tables;
    }

    public function where($column, $operator, $value)
    {
        $this->where[] = array(
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
        );
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