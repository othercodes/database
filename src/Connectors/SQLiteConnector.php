<?php

namespace OtherCode\Database\Connectors;

/**
 * Class SQLiteConnector
 * @package OtherCode\Database\Connectors
 */
class SQLiteConnector extends Connector
{

    /**
     * @param array $config
     * @return \PDO
     * @throws \InvalidArgumentException
     */
    public function connect(array $config)
    {
        $options = $this->getOptions($config);

        if ($config['dbname'] == ':memory:') {
            return $this->createConnection('sqlite::memory:', $config, $options);
        }

        $path = realpath($config['dbname']);

        if ($path === false) {
            throw new \InvalidArgumentException("Database (${config['dbname']}) does not exist.");
        }

        return $this->createConnection("sqlite:{$path}", $config, $options);
    }

}