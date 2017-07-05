<?php

namespace OtherCode\Database\Connectors;

/**
 * Class MysqlConnector
 * @package OtherCode\Database\Connectors
 */
class MySQLConnector extends Connector
{

    /**
     * Default charset
     * @var string
     */
    private $charset = 'utf8';

    /**
     * Default collation
     * @var string
     */
    private $collation = 'utf8_unicode_ci';

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

        $charset = isset($config['charset']) ? $config['charset'] : $this->charset;
        $collation = isset($config['collation']) ? $config['collation'] : $this->collation;

        $names = "set names '$charset'" . (!is_null($collation) ? " collate '$collation'" : '');
        $connection->prepare($names)->execute();

        if (isset($config['timezone'])) {
            $connection->prepare('set time_zone="' . $config['timezone'] . '"')->execute();
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
        if (isset($config['port'])) {
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}";
        } else {
            $dsn = "mysql:host={$config['host']};dbname={$config['dbname']}";
        }

        return $dsn;
    }
}