<?php

class ConnecionTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiation()
    {
        $db = new \OtherCode\Database\Database();
        $this->assertInstanceOf('\OtherCode\Database\Database', $db);
        return $db;
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
            'username' => 'root',
            'password' => ''
        ), 'mysql');

        $this->assertInstanceOf('\PDO', $db->getConnection());
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
            'username' => 'postgres',
            'password' => ''
        ), 'pgsql');

        $this->assertInstanceOf('\PDO', $db->getConnection('pgsql'));
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
    }
}