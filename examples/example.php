<?php 

require_once "../autoload.php";

use OtherCode\Database\Database;
use OtherCode\Database\Query\Query;

$db = new Database();

$db->addConnection(array(
    'driver' => 'mysql',
    'host' => 'localhost',
    'dbname' => 'test',
    'username' => 'root',
    'password' => 'root'
),'test');

$db->addConnection(array(
    'driver' => 'sqlite',
    'dbname' => 'database.sqlite',
),'cache');

$db->query(true);

var_dump($db);

$query = new Query();
$query->select();
$query->from(array('ts_users'));
$query->where('name','=','Walter');

var_dump($query);

print $query;
