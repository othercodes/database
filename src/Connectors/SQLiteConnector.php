<?php

namespace OtherCode\Database\Connectors;

use PDO;
use InvalidArgumentException;

class SQLiteConnector extends Connector
{

    /**
     * @param array $config
     * @return null|PDO
     */
    public function connect(array $config)
    {
        $options = $this->getOptions($config);

        if ($config['dbname'] == ':memory:') {
            return $this->createConnection('sqlite::memory:', $config, $options);
        }

        $path = realpath($config['dbname']);

        if ($path === false) {
            throw new InvalidArgumentException("Database (${config['dbname']}) does not exist.");
        }

        return $this->createConnection("sqlite:{$path}", $config, $options);
    }

}