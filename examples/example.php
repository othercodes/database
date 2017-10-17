<?php

require_once "../vendor/autoload.php";

$db = new OtherCode\Database\Database();

try {

    $db->addConnection(array(
        'driver' => 'mysql',
        'host' => 'localhost',
        'dbname' => 'test',
        'username' => 'test',
        'password' => 'test'
    ), 'default');

    $query = $db->getQuery();
    $query->select();
    $query->from('ts_users');
    $query->where('name', '=', ':name');
    $query->where('surname', '=', ':surname');
    $query->orWhere('name', '=', $query->quote('Sheldon'));

    $db->setQuery($query);
    $db->execute(array(
        ':name' => 'Walter',
        ':surname' => 'White'
    ));

    $result = $db->loadObjectList();

    var_dump($result);

} catch (\Exception $e) {

    print $e->getMessage();

}



