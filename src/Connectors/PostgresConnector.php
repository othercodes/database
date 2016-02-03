<?php

namespace OtherCode\Database\Connectors;

use PDO;

/**
 * Class PostgresConnector
 * @package OtherCode\Database\Connectors
 */
class PostgresConnector extends Connector
{

    /**
     * Create the actual connection.
     * @param array $config
     * @return null|PDO
     */
    public function connect(Array $config)
    {
        $dsn = $this->getDsn($config);
        $options = $this->getOptions($config);

        $connection = $this->createConnection($dsn, $config, $options);

        $charset = $config['charset'];
        $connection->prepare("set names '$charset'")->execute();

        if (isset($config['timezone'])) {
            $timezone = $config['timezone'];
            $connection->prepare("set time zone '$timezone'")->execute();
        }

        return $connection;
    }

    /**
     * Return the base DSN for the connection.
     * @param array $config
     * @return string
     */
    protected function getDsn(Array $config)
    {
        $dsn = "pgsql:{$config['host']}dbname={$config['dbname']}";

        if (isset($config['port'])) {
            $dsn .= ";port={$config['port']}";
        }

        if (isset($config['sslmode'])) {
            $dsn .= ";sslmode={$config['sslmode']}";
        }

        return $dsn;
    }
}