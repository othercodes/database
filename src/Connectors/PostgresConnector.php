<?php

namespace OtherCode\Database\Connectors;

/**
 * Class PostgresConnector
 * @package OtherCode\Database\Connectors
 */
class PostgresConnector extends Connector
{

    /**
     * Create the actual connection.
     * @param array $config
     * @return \PDO
     */
    public function connect(Array $config)
    {
        $dsn = $this->getDSN($config);
        $options = $this->getOptions($config);

        $connection = $this->createConnection($dsn, $config, $options);

        if (isset($config['charset'])) {
            $connection->prepare("set names '" . $config['charset'] . "'")->execute();
        }

        if (isset($config['timezone'])) {
            $connection->prepare("set time zone '" . $config['timezone'] . "'")->execute();
        }

        return $connection;
    }

    /**
     * Return the base DSN for the connection.
     * @param array $config
     * @return string
     */
    protected function getDSN(Array $config)
    {
        $dsn = "pgsql:host={$config['host']};dbname={$config['dbname']}";

        if (isset($config['port'])) {
            $dsn .= ";port={$config['port']}";
        }

        if (isset($config['sslmode'])) {
            $dsn .= ";sslmode={$config['sslmode']}";
        }

        return $dsn;
    }
}