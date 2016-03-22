<?php 

require_once "../vendor/autoload.php";

use OtherCode\Database\Database;
use OtherCode\Database\Query\Query;

$db = new Database();

$db->addConnection(array(
    'driver' => 'mysql',
    'host' => 'localhost',
    'dbname' => 'test',
    'username' => 'root',
    'password' => 'root'
),'foro');

$db->addConnection(array(
    'driver' => 'sqlite',
    'dbname' => 'database.sqlite',
),'cache');

$db->query(true);



$query = new Query();
$query->select();
$query->from(array('ts_users'));
$query->where('name','=','Walter');

var_dump($query);

print $query;