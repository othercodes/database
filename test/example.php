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
    'password' => ''
),'foro');

$db->addConnection(array(
    'driver' => 'sqlite',
    'dbname' => 'database.sqlite',
),'cache');


$query = new Query();
$query->update(array('name'));
$query->setValues(array('name'));
$query->where('id','=',2);
$query->where('name','=','Walter');

var_dump($query);

print $query;