<?php

class QueryMySQLTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @depends testMySQLConnection
     */
    public function testSelectAll()
    {}

    /**
     * @depends testMySQLConnection
     */
    public function testSeletWhere()
    {}
}