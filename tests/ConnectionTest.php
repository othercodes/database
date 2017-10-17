<?php

class ConnectionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return \OtherCode\Database\Database
     */
    public function testInstantiation()
    {
        $db = new \OtherCode\Database\Database();
        $this->assertInstanceOf('\OtherCode\Database\Database', $db);
        return $db;
    }

    /**
     * @depends testInstantiation
     * @expectedException \OtherCode\Database\Exceptions\ConnectionException
     * @param \OtherCode\Database\Database $db
     */
    public function testInvalidDriver(\OtherCode\Database\Database $db)
    {
        $db->addConnection(array(
            'driver' => 'non-valid-driver',
            'host' => 'localhost',
            'dbname' => '',
            'username' => '',
            'password' => ''
        ), 'fail');
    }

    /**
     * @depends testInstantiation
     * @param \OtherCode\Database\Database $db
     */
    public function testGetNllConnection(\OtherCode\Database\Database $db)
    {
        $this->assertNull($db->getConnection('none'));
    }

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
                'dbname' => dirname(__DIR__) . '/examples/test.sqlite',
            )
        ));

        $this->assertInstanceOf('\PDO', $db->getConnection('mysql'));
        $this->assertInstanceOf('\PDO', $db->getConnection('pgsql'));
        $this->assertInstanceOf('\PDO', $db->getConnection('sqlite'));

        return $db;
    }

    /**
     * @depends testInstantiationBatch
     * @param \OtherCode\Database\Database $db
     */
    public function testOnConnection(\OtherCode\Database\Database $db)
    {
        $default = $db->getConnection()->getAttribute(\PDO::ATTR_DRIVER_NAME);
        $this->assertEquals('mysql', $default);

        $db->on('sqlite');

        $default = $db->getConnection()->getAttribute(\PDO::ATTR_DRIVER_NAME);
        $this->assertEquals('sqlite', $default);
    }
}