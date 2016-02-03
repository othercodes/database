<?php

namespace OtherCode\Database;

use InvalidArgumentException;

class Database
{

    /**
     * List of available connections
     * @var array
     */
    private $connections = array();

    /**
     * Create a new PDO connection
     * @param array $config
     * @param string $name
     */
    public function addConnection(Array $config, $name = 'default')
    {
        $connectors = array(
            'mysql' => 'Mysql',
            'pgsql' => 'Postgres',
            'sqlite' => 'SQLite'
        );

        if(!isset($config['driver']) || !array_key_exists($config['driver'],$connectors)){
            throw new InvalidArgumentException("The selected driver is not valid.");
        }

        $connector = "OtherCode\\Database\\Connectors\\" . $connectors[$config['driver']] . "Connector";
        $connection = new $connector();

        $this->connections[$name] = $connection->connect($config);

    }




}