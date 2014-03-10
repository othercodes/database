<?php 
include 'configuration.php';
include 'database.pdo.class.php';

// get a instance
$db = database::getInstance();

// query of single field
$sql = "SELECT nombre FROM usuarios WHERE id = 1";
$db->query($sql);
$nombre = $db->loadResult();
var_dump($nombre);

// query of single field, list
$sql = "SELECT nombre FROM usuarios";
$db->query($sql);
$nombres = $db->loadColumn();
var_dump($nombres);

// get the numbers of rows returned in the SELECT
$count = $db->getCountRows();
var_dump($count);

// simple query, list of objects
$sql = "SELECT * FROM usuarios";
$db->query($sql);
$usuarios = $db->loadAssocList();
var_dump($usuarios);

// simple query JSON List
$sql = "SELECT * FROM usuarios";
$db->query($sql);
$usuarios = $db->loadJsonObjectList();
var_dump($usuarios);

// query with parameters, one object
$sql = "SELECT * FROM usuarios WHERE id = :id";
$params = array(
    ':id' => 1
);
$db->query($sql,$params);
$usuario = $db->loadObject();
var_dump($usuario);

//insert example
$sql = "INSERT INTO usuarios(id,nombre,apellido) VALUES (NULL, :nombre, :apellido)";
$params = array(
    ':nombre' => 'Tim',
    ':apellido' => 'Burton'
);
$db->query($sql,$params);
if($db->getAffectedRows() == 1){
    echo "SUCCESS";
} else {
    $e = $db->getError();
    echo "ERROR ".$e['code']. ": ".$e['desc'];
}

// transacction example
$db->startTransaction();
$db->query("INSERT INTO usuarios VALUES (NULL, 'Tyrande', 'Whisperwind')");
$db->query("INSERT INTO usuarios VALUES (NULL, 'Vincent', 'Vega')");
$status = $db->endTransaction();
if($status == 1){
    echo "SUCCESS";
} else {
    echo "ERROR";
}

// simple query, result in XML Document
$sql = "SELECT * FROM usuarios";
$db->query($sql);
$usuarios = $db->loadXmlDocument('1.0','UTF-8');
var_dump($usuarios);
?>
