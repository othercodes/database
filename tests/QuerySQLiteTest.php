<?php

class QuerySQLiteTest extends \PHPUnit\Framework\TestCase
{

    public function testSQLiteConnection()
    {
        $db = new \OtherCode\Database\Database(array(
                'default' => array(
                    'driver' => 'sqlite',
                    'dbname' => 'examples/test.sqlite',
                ))
        );

        $this->assertInstanceOf('\OtherCode\Database\Database', $db);
        $this->assertInstanceOf('\PDO', $db->getConnection());

        return $db;
    }

    /**
     * @depends testSQLiteConnection
     */
    public function testSelectAll(\OtherCode\Database\Database $db)
    {
        $query = $db->getQuery();

        $query->select(array('*'))->from('ts_users');

        $list = $db->setQuery($query)
            ->execute()
            ->loadObjectList();

        $this->assertInternalType('array', $list);
        $this->assertCount(2, $list);
    }

    /**
     * @depends testSQLiteConnection
     */
    public function testSelectWhere(\OtherCode\Database\Database $db)
    {
        $query = $db->getQuery();

        $query->select(array('*'))
            ->from('ts_users')
            ->where('name', '=', 'Walter', true);

        $list = $db->setQuery($query)
            ->execute()
            ->loadObject();

        $this->assertInternalType('object', $list);
    }
}