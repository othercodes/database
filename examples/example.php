<?php

require_once "../autoload.php";

$db = new OtherCode\Database\Database();

try {

    $db->addConnection(array(
        'driver' => 'mysql',
        'host' => 'localhost',
        'dbname' => 'test',
        'username' => 'root',
        'password' => 'root'
    ));

} catch (OtherCode\Database\Exceptions\ConnectionException $e) {

    print $e->getMessage();
}

$query = $db->getQuery();
$query->select();
$query->from('ts_users');
$query->where('name', '=', 'Walter');

$db->setQuery($query)
    ->on('default')
    ->execute();

var_dump($db);