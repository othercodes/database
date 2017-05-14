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
     * The query to be executed
     * @var \OtherCode\Database\Query\Query
     */
    protected $query;

    /**
     * Prepared Statement Object
     * @var \PDOStatement
     */
    protected $stmt;

    /**
     * Create a new PDO connection
     * @param array $config
     * @param string $name
     * @return $this
     * @throws \OtherCode\Database\Exceptions\ConnectionException
     */
    public function addConnection(Array $config, $name = 'default')
    {
        $connectors = array(
            'mysql' => 'Mysql',
            'pgsql' => 'Postgres',
            'sqlite' => 'SQLite'
        );

        if (!isset($config['driver']) || !array_key_exists($config['driver'], $connectors)) {
            throw new \OtherCode\Database\Exceptions\ConnectionException("The selected driver is not valid.");
        }

        $connector = "OtherCode\\Database\\Connectors\\" . $connectors[$config['driver']] . 'Connector';
        $connection = new $connector();

        $this->connections[$name] = $connection->connect($config);
        return $this;
    }

    /**
     * Return a connection
     * @param string $name
     * @return \PDO|null
     */
    public function getConnection($name = 'default')
    {
        if(array_key_exists($name, $this->connections)){
            return $this->connections[$name];
        }
        return null;
    }

    /**
     * Return a new Query instance
     * @param boolean $new
     * @return Query\Query
     */
    public function getQuery($new = false)
    {
        if ($this->query !== null && !$new) {
            return $this->query;
        }

        return new \OtherCode\Database\Query\Query();
    }

    /**
     * Set and execute a query
     * @param \OtherCode\Database\Query\Query|string $query
     * @return $this
     */
    public function setQuery($query)
    {
        if (is_string($query) || $query instanceof \OtherCode\Database\Query\Query) {
            $this->query = $query;
        }
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

    /**
     * Execute the current query
     * @param null|array $params
     * @throws \OtherCode\Database\Exceptions\DatabaseException
     * @return $this
     */
    public function execute($params = null)
    {
        $sql = $this->query instanceof \OtherCode\Database\Query\Query ? $this->query->compile() : $this->query;

        try {

            $this->stmt = $this->connections[$this->defaultConnection]->prepare($sql);
            $this->stmt->execute($params);

        } catch (\PDOException $e) {

            throw new \OtherCode\Database\Exceptions\DatabaseException("Execute error: " . $e->getMessage(), $e->getCode());
        }

        return $this;
    }

    /**
     * Return a single filed.
     * @param int $index
     * @return mixed|null
     */
    public function loadResult($index = 0)
    {
        if ($this->stmt === null) {
            return null;
        }

        $singleResult = $this->stmt->fetch(\PDO::FETCH_NUM);
        return $singleResult[$index];
    }

    /**
     * Return a single column
     * @param int $index
     * @return array|null
     */
    public function loadColumn($index = 0)
    {
        if ($this->stmt === null) {
            return null;
        }

        $columnList = array();
        while ($row = $this->stmt->fetch(\PDO::FETCH_NUM)) {
            $columnList[] = $row[$index];
        }
        return $columnList;
    }

    /**
     * Return the query result in object format
     * @param string $class_name
     * @return mixed|null
     */
    public function loadObject($class_name = "stdClass")
    {
        if ($this->stmt === null) {
            return null;
        }

        return $this->stmt->fetchObject($class_name);
    }

    /**
     * Return the query result in objects list format
     * @param string $class_name
     * @return array|null
     */
    public function loadObjectList($class_name = "stdClass")
    {
        if ($this->stmt === null) {
            return null;
        }

        $objectList = array();
        while ($object = $this->stmt->fetchObject($class_name)) {
            $objectList[] = $object;
        }
        return $objectList;
    }

    /**
     * Return a single record as associative array
     * @return array|null
     */
    public function loadAssocRow()
    {
        if ($this->stmt === null) {
            return null;
        }

        return $this->stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Return a list of associative arrays
     * @return array|null
     */
    public function loadAssocList()
    {
        if ($this->stmt === null) {
            return null;
        }

        $assocList = array();
        while ($row = $this->stmt->fetch(\PDO::FETCH_ASSOC)) {
            $assocList[] = $row;
        }
        return $assocList;
    }

    /**
     * Returna single record as indexed array
     * @return array|null
     */
    public function loadIndexedRow()
    {
        if ($this->stmt === null) {
            return null;
        }

        return $this->stmt->fetch(\PDO::FETCH_NUM);
    }

    /**
     * Return a list of indexed arrays
     * @return array|null
     */
    public function loadIndexedList()
    {
        if ($this->stmt === null) {
            return null;
        }

        $indexedList = array();
        while ($row = $this->stmt->fetch(\PDO::FETCH_NUM)) {
            $indexedList[] = $row;
        }
        return $indexedList;
    }
}