<?php

class QueryTest extends \PHPUnit\Framework\TestCase
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

        $this->assertInstanceOf('\PDO', $db->getConnection());
        $this->assertInstanceOf('\PDO', $db->getConnection('pgsql'));
        $this->assertInstanceOf('\PDO', $db->getConnection('sqlite'));

        return $db;
    }

    /**
     * @depends testInstantiationBatch
     */
    public function testSelectAll(\OtherCode\Database\Database $db)
    {
        $connections = array('mysql', 'pgsql', 'sqlite');
        foreach ($connections as $connection) {

            $query = $db->getQuery();
            $query->select()
                ->from('ts_users');

            $list = $db->setQuery($query)
                ->on($connection)
                ->execute()
                ->loadObjectList();

            $this->assertInternalType('array', $list);
            $this->assertCount(2, $list);

        }
    }

    /**
     * @depends testInstantiationBatch
     */
    public function testSelectWhere(\OtherCode\Database\Database $db)
    {
        $connections = array('mysql', 'pgsql', 'sqlite');
        foreach ($connections as $connection) {
            $query = $db->getQuery();
            $query->select()
                ->from('ts_users')
                ->where('name', '=', 'Walter');

            $list = $db->setQuery($query)
                ->on($connection)
                ->execute()
                ->loadObject();

            $this->assertInternalType('object', $list);
        }
    }
}