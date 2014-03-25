<?php 
include 'configuration.php';
include 'database.pdo.class.php';

// get a instance, singletone pattern
$db = database::getInstance();

// query of single field
$db->query("SELECT nombre FROM usuarios WHERE id = 1");
$nombre = $db->loadResult();
var_dump($nombre);

// query of single field, list
$db->query("SELECT nombre FROM usuarios");
$nombres = $db->loadColumn();
var_dump($nombres);

// get the numbers of rows returned in the SELECT
$count = $db->getCountRows();
var_dump($count);

// simple query, list of objects
$db->query("SELECT * FROM usuarios");
$usuarios = $db->loadAssocList();
var_dump($usuarios);

// simple query JSON List
$db->query("SELECT * FROM usuarios");
$usuarios = $db->loadJsonObjectList();
var_dump($usuarios);

// query with parameters (named placeholders), one object
$sql = "SELECT * FROM usuarios WHERE id = :id";
$params = array(':id' => 1);
// or query with parameters (positional ? placeholders)
$sql = "SELECT * FROM usuarios WHERE id = ?";
$params = array(1 => 1);
$db->query($sql,$params);
// instantiating a predefinied user class, leave empty to load a StdClass
$usuario = $db->loadObject('user');
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

// simple query, result in XML Format
$db->query("SELECT * FROM usuarios");
$usuarios = $db->loadXmlDocument();
var_dump($usuarios);
// or to export to an external xml file
$db->query("SELECT * FROM usuarios");
$usuarios = $db->loadXmlDocument('name.xml');
var_dump($usuarios);
?>
