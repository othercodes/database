<?php 
include 'configuration.php';
include 'database.pdo.class.php';

// get a instance
$db = database::getInstance();

// simple query, list of objects
$sql = "SELECT * FROM usuarios";
$db->query($sql);
$usuarios = $db->loadObjectList();
var_dump($usuarios);

// simple query JSON List
$sql = "SELECT * FROM usuarios";
$db->query($sql);
$usuario = $db->loadJsonObjectList();
var_dump($usuario);

// query with parameters, one object
$sql = "SELECT * FROM usuarios WHERE id = :id";
$params = array(
    ':id' => 1
);
$db->query($sql,$params);
$usuario = $db->loadObject();
var_dump($usuario);

// query with parameters, one assoc array
$sql = "SELECT * FROM usuarios WHERE id = :id";
$params = array(
    ':id' => 2
);
$db->query($sql,$params);
$usuario = $db->loadAssoc();
var_dump($usuario);
?>
