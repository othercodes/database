<?php 

require_once "../vendor/autoload.php";

use OtherCode\Database\Database;

$db = new Database();
$db->addConnection(array(
    'driver' => 'mysql',
    'host' => 'localhost',
    'dbname' => 'test',
    'username' => 'root',
    'password' => 'root'
),'mysql');
$db->addConnection(array(
    'driver' => 'sqlite',
    'dbname' => 'database.sqlite',
),'sqlite');

var_dump($db);