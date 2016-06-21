<?php

namespace OtherCode\Database;

/**
 * Class Database
 * @package OtherCode\Database
 */
class Database
{
    /**
     * Default connect to launch the queries
     * @var string
     */
    private $defaultConnection = 'default';

    /**
     * List of available connections
     * @var array
     */
    private $connections = array();

    /**
     * Create a new PDO connection
     * @param array $config
     * @param string $name
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function addConnection(Array $config, $name = 'default')
    {
        $connectors = array(
            'mysql' => 'Mysql',
            'pgsql' => 'Postgres',
            'sqlite' => 'SQLite'
        );

        if (!isset($config['driver']) || !array_key_exists($config['driver'], $connectors)) {
            throw new \InvalidArgumentException("The selected driver is not valid.");
        }

        $connector = "OtherCode\\Database\\Connectors\\" . $connectors[$config['driver']] . 'Connector';
        $connection = new $connector();

        $this->connections[$name] = $connection->connect($config);
        return $this;
    }

    /**
     * Return a new Query instance
     * @return Query\Query
     */
    public function getQuery()
    {
        return new \OtherCode\Database\Query\Query();
    }

    /**
     * Set and execute a query
     * @param \OtherCode\Database\Query\Query $query
     * @return $this
     */
    public function setQuery(\OtherCode\Database\Query\Query $query)
    {

        return $this;
    }

    /**
     * Set the new default connection
     * @param $connection
     * @return $this
     */
    public function on($connection)
    {
        if (array_key_exists($connection, $this->connections)) {
            $this->defaultConnection = $connection;
        }
        return $this;
    }

    
    public function loadResult()
    {
    }

    public function loadColumn()
    {
    }

    public function loadObject($class_name = "stdClass")
    {
    }

    public function loadObjectList($class_name = "stdClass")
    {
    }

    public function loadAssocRow()
    {
    }

    public function loadAssocList()
    {
    }

    public function loadIndexedRow()
    {
    }

    public function loadIndexedList()
    {
    }
}