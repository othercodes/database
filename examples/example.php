<?php

require_once "../autoload.php";

$db = new OtherCode\Database\Database();

try {

    $db->addConnection(array(
        'driver' => 'mysql',
        'host' => 'localhost',
        'dbname' => 'test',
        'username' => 'test',
        'password' => ''
    ));

    $query = $db->getQuery();
    $query->select();
    $query->from('ts_users');
    $query->where('name', '=', ':name');

    $db->setQuery($query);
    $db->execute(array(':name' => 'Walter'));

    $result = $db->loadObject();

    var_dump($result);

} catch (\Exception $e) {

    print $e->getMessage();

}



