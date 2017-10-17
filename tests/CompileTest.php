<?php

class CompileTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @return \OtherCode\Database\Database
     */
    public function testInstantiationBatch()
    {
        $db = new \OtherCode\Database\Database(array(
            'mysql' => array(
                'driver' => 'mysql',
                'host' => 'localhost',
                'dbname' => 'test',
                'username' => 'test',
                'password' => 'test'
            ),
            'pgsql' => array(
                'driver' => 'pgsql',
                'host' => 'localhost',
                'dbname' => 'test',
                'username' => 'test',
                'password' => 'test'
            ),
            'sqlite' => array(
                'driver' => 'sqlite',
                'dbname' => 'examples/test.sqlite',
            )
        ));

        $this->assertInstanceOf('\PDO', $db->getConnection());
        $this->assertInstanceOf('\PDO', $db->getConnection('pgsql'));
        $this->assertInstanceOf('\PDO', $db->getConnection('sqlite'));

        return $db;
    }

    /**
     * @depends testInstantiationBatch
     */
    public function testGetCompilers(\OtherCode\Database\Database $db)
    {
        $connections = array('mysql', 'pgsql', 'sqlite');
        foreach ($connections as $connection) {
            $this->assertInstanceOf('\OtherCode\Database\Query\Compilers\Compiler', $db->getCompiler($connection));
        }
    }
}