<?php

namespace OtherCode\Database\Connectors;

/**
 * Class Connector
 * @package OtherCode\Database\Connectors
 */
abstract class Connector
{

    /**
     * Options
     * @var array
     */
    protected $options = array(
        \PDO::ATTR_CASE => \PDO::CASE_NATURAL,
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_ORACLE_NULLS => \PDO::NULL_NATURAL,
        \PDO::ATTR_STRINGIFY_FETCHES => false,
    );

    /**
     * @param $dsn
     * @param array $config
     * @param array $options
     * @return \PDO
     * @throws \PDOException
     * @throws \Othercode\Database\Exceptions\ConnectionException
     */
    public function createConnection($dsn, Array $config, Array $options)
    {

        $pdo = null;

        $username = isset($config['username']) ? $config['username'] : null;
        $password = isset($config['password']) ? $config['password'] : null;

        try {
            $pdo = new \PDO($dsn, $username, $password, $options);
        } catch (\Exception $e) {

            /**
             * @TODO maybe reconnect if fail?
             */
            throw new \OtherCode\Database\Exceptions\ConnectionException($e->getMessage(),$e->getCode());
        }

        return $pdo;
    }

    /**
     * @param array $config
     * @return array
     */
    public function getOptions(Array $config)
    {
        $options = isset($config['options']) ? $config['options'] : array();
        return array_diff_key($this->options, $options) + $options;
    }

}