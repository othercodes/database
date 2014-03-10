<?php 
include 'configuration.php';
include 'database.pdo.class.php';

// get a instance
$db = database::getInstance();

// simple query, list of objects
$sql = "SELECT * FROM usuarios";
$db->query($sql);
$usuarios = $db->loadAssocList();
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
?>
