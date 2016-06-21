<?php 

require_once "../autoload.php";

$db = new OtherCode\Database\Database();

$db->addConnection(array(
    'driver' => 'mysql',
    'host' => 'localhost',
    'dbname' => 'test',
    'username' => 'root',
    'password' => 'root'
),'test');

var_dump($db);

$query = $db->getQuery();
$query->select();
$query->from('ts_users');
$query->where('name','=','Walter');

print $query;

$query = $db->getQuery(true);
$query->select();
$query->from('ts_users');
$query->where('surname','=','White');

print $query;