<?php

class ConnectionTest extends \PHPUnit\Framework\TestCase
{

    public function testInstantiation()
    {
        $db = new \OtherCode\Database\Database();
        $this->assertInstanceOf('\OtherCode\Database\Database', $db);
        return $db;
    }

    public function testInstantiationBatch()
    {
        $db = new \OtherCode\Database\Database(array(
            'mysql' => array(
                'driver' => 'mysql',
                'host' => 'localhost',
                'dbname' => 'test',
                'username' => 'test',
                'password' => ''
            ),
            'pgsql' => array(
                'driver' => 'pgsql',
                'host' => 'localhost',
                'dbname' => 'test',
                'username' => 'test',
                'password' => ''
            ),
            'sqlite' => array(
                'driver' => 'sqlite',
                'dbname' => 'examples/test.sqlite',
            )
        ));

        $this->assertInstanceOf('\PDO', $db->getConnection('mysql'));
        $this->assertInstanceOf('\PDO', $db->getConnection('pgsql'));
        $this->assertInstanceOf('\PDO', $db->getConnection('sqlite'));
    }

    /**
     * @depends testInstantiation
     */
    public function testMySQLConnection(\OtherCode\Database\Database $db)
    {
        $db->addConnection(array(
            'driver' => 'mysql',
            'host' => 'localhost',
            'dbname' => 'test',
            'username' => 'test',
            'password' => ''
        ), 'mysql');

        $this->assertInstanceOf('\PDO', $db->getConnection('mysql'));

        return $db;
    }

    /**
     * @depends testInstantiation
     */
    public function testPostgreSQLConnection(\OtherCode\Database\Database $db)
    {
        $db->addConnection(array(
            'driver' => 'pgsql',
            'host' => 'localhost',
            'dbname' => 'test',
            'username' => 'test',
            'password' => ''
        ), 'pgsql');

        $this->assertInstanceOf('\PDO', $db->getConnection('pgsql'));

        return $db;
    }

    /**
     * @depends testInstantiation
     */
    public function testSQLiteConnection(\OtherCode\Database\Database $db)
    {
        $db->addConnection(array(
            'driver' => 'sqlite',
            'dbname' => 'examples/test.sqlite',
        ), 'sqlite');

        $this->assertInstanceOf('\PDO', $db->getConnection('sqlite'));

        return $db;
    }
}