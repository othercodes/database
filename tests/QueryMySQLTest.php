<?php

class QueryMySQLTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @depends testMySQLConnection
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
     * @depends testMySQLConnection
     */
    public function testSeletWhere(\OtherCode\Database\Database $db)
    {
        $query = $db->getQuery();

        $query->select(array('*'))
            ->from('ts_user')
            ->where('name', '=', 'Walter');

        $list = $db->setQuery($query)
            ->execute()
            ->loadObject();

        $this->assertInternalType('stdClass', $list);
    }
}