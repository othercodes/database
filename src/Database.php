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
    private $defaultConnection;

    /**
     * List of available connections
     * @var \PDO[]
     */
    private $connections = array();

    /**
     * Compiler system for the query
     * @var \OtherCode\Database\Query\Compilers\Compiler[]
     */
    private $compilers = array();

    /**
     * Connectors list
     * @var array
     */
    protected $connectors = array(
        'mysql' => 'MySQL',
        'pgsql' => 'Postgres',
        'sqlite' => 'SQLite'
    );

    /**
     * The query to be executed
     * @var \OtherCode\Database\Query
     */
    protected $query;

    /**
     * Prepared Statement Object
     * @var \PDOStatement
     */
    protected $stmt;

    /**
     * Database constructor.
     * @param array $configs
     */
    public function __construct(array $configs = null)
    {
        if (isset($configs)) {
            $counter = 0;
            foreach ($configs as $name => $config) {
                $this->addConnection($config, $name, $counter++ === 0 ? true : false);
            }
        }
    }

    /**
     * Create a new PDO connection
     * @param array $config
     * @param string $name
     * @param bool $default
     * @return $this
     * @throws \OtherCode\Database\Exceptions\ConnectionException
     */
    public function addConnection(array $config, $name, $default = false)
    {
        if (!isset($config['driver']) || !array_key_exists($config['driver'], $this->connectors)) {
            throw new \OtherCode\Database\Exceptions\ConnectionException("The selected driver is not valid.");
        }

        $connector = "OtherCode\\Database\\Connectors\\" . $this->connectors[$config['driver']] . "Connector";
        $this->connections[$name] = (new $connector())->connect($config);

        if (!array_key_exists($this->connectors[$config['driver']], $this->compilers)) {
            $compiler = "OtherCode\\Database\\Query\\Compilers\\" . $this->connectors[$config['driver']] . "Compiler";
            $this->compilers[$this->connectors[$config['driver']]] = new $compiler();
        }

        if ($default === true) {
            $this->defaultConnection = $name;
        }

        return $this;
    }

    /**
     * Return a connection
     * @param string|null $name
     * @return \PDO|null
     */
    public function getConnection($name = null)
    {
        if (empty($name)) {
            return $this->connections[$this->defaultConnection];
        }

        if (array_key_exists($name, $this->connections)) {
            return $this->connections[$name];
        }
        return null;
    }

    /**
     * Return the compiler for a connection
     * @param string $name
     * @return Query\Compilers\Compiler
     */
    public function getCompiler($name = null)
    {
        $driver = $this->getConnection($name)->getAttribute(\PDO::ATTR_DRIVER_NAME);
        return $this->compilers[$this->connectors[$driver]];

    }

    /**
     * Return a new Query instance
     * @param boolean $new
     * @return \OtherCode\Database\Query
     */
    public function getQuery($new = true)
    {
        if (isset($this->query) && $new === false) {
            return $this->query;
        }
        return new \OtherCode\Database\Query();
    }

    /**
     * Set and execute a query
     * @param \OtherCode\Database\Query|string $query
     * @return $this
     */
    public function setQuery($query)
    {
        if ($query instanceof \OtherCode\Database\Query) {
            $this->query = $query;
        }
        return $this;
    }

    /**
     * Set the new default connection
     * @param string $connection
     * @throws \OtherCode\Database\Exceptions\ConnectionException
     * @return $this
     */
    public function on($connection)
    {
        if (!array_key_exists($connection, $this->connections)) {
            throw new \OtherCode\Database\Exceptions\ConnectionException("The selected connection is not available.");
        }

        $this->defaultConnection = $connection;
        return $this;
    }

    /**
     * Execute the current query
     * @param null|array $params
     * @param null|string $query
     * @throws \OtherCode\Database\Exceptions\DatabaseException
     * @return $this
     */
    public function execute($params = null, $query = null)
    {
        try {

            if (!isset($query)) {
                $query = $this->getCompiler()->compile($this->query);
            }

            $this->stmt = $this->connections[$this->defaultConnection]->prepare($query);
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
     * Return single record as indexed array
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