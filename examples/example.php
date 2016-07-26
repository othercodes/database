<?php

require_once "../autoload.php";

$db = new OtherCode\Database\Database();

try {

    $db->addConnection(array(
        'driver' => 'mysql',
        'host' => 'localhost',
        'dbname' => 'test',
        'username' => 'root',
        'password' => ''
    ));

    $query = $db->getQuery();
    $query->select();
    $query->from('ts_users');
    $query->where('name', '=', 'Walter');

    $db->setQuery($query);
    $db->execute();

    $result = $db->loadObject();

    var_dump($db, $result);

} catch (\Exception $e) {

    print $e->getMessage();

}



