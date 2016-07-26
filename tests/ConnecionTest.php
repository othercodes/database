<?php

class ConnecionTest extends \PHPUnit_Framework_TestCase
{

    public function testMySQLConnection()
    {
        $db = new \OtherCode\Database\Database();

        $db->addConnection(array(
            'driver' => 'mysql',
            'host' => 'localhost',
            'dbname' => 'test',
            'username' => 'root',
            'password' => ''
        ));

        $this->assertInstanceOf('\OtherCode\Database\Database', $db);
    }

    public function testPostgreSQLConnection()
    {
        $db = new \OtherCode\Database\Database();

        $db->addConnection(array(
            'driver' => 'pgsql',
            'host' => 'localhost',
            'dbname' => 'test',
            'username' => 'postgres',
            'password' => ''
        ));

        $this->assertInstanceOf('\OtherCode\Database\Database', $db);
    }
}