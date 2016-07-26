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
            'password' => 'root'
        ));

        $this->assertInstanceOf('\OtherCode\Database\Database', $db);

    }
}