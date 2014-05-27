<?php 
include 'configuration.php';
include 'database.pdo.class.php';

// this are many examples of the usage of this class
// get a instance, singletone pattern, always required
$db = database::getInstance();

// example of query of single field
$db->query("SELECT name FROM #__users WHERE id = 1");
$name = $db->loadResult();
var_dump($name);

// example of query of single field, list
$db->query("SELECT name FROM #__users");
$names = $db->loadColumn();
var_dump($names);

// example of get the numbers of rows returned in the SELECT
$count = $db->getCountRows();
var_dump($count);

// example of simple query, list of assoc arrays
$db->query("SELECT * FROM #__users");
$users = $db->loadAssocList();
var_dump($users);

// example of simple query JSON List
$db->query("SELECT * FROM #__users");
$users = $db->loadJsonObjectList();
var_dump($users);

// example of query with parameters (named placeholders), one object
$sql = "SELECT * FROM #__users WHERE id = :id";
$params = array(':id' => 1);
// or example of query with parameters (? placeholders), one object
$sql = "SELECT * FROM #__users WHERE id = ?";
$params = array(1);
$db->query($sql,$params);
// instantiating a StdClass
$user = $db->loadObject();
var_dump($user);

// insert example
$sql = "INSERT INTO #__users(id,name,surname) VALUES (NULL, :name, :surname)";
$params = array(
    ':name' => 'Tim',
    ':surname' => 'Burton'
);
$db->query($sql,$params);
if($db->getAffectedRows() == 1){
    echo "Success<br />\n";
} else {
    $e = $db->getError();
    echo "Error ".$e['code']. ": ".$e['desc']."<br />\n";
}

// transacction example
$db->startTransaction();
$db->query("INSERT INTO #__users(id,name,surname) VALUES (NULL, 'Tyrande', 'Whisperwind')");
$db->query("INSERT INTO #__users(id,name,surname) VALUES (NULL, 'Vincent', 'Vega')");
$status = $db->endTransaction();
if($status == 1){
    echo "Success<br />\n";
} else {
    echo "Error<br />\n";
}

// example of simple query, result in XML Format
$db->query("SELECT * FROM #__users");
$users = $db->loadXmlDocument();
// this will dump a string with the xml result
echo $users;

echo "<br />\n";

// or to export to an external xml file
$db->query("SELECT * FROM #__users");
// no format, all in one line
$users = $db->loadXmlDocument('users.xml');


// example of data export to a csv file.
$db->query("SELECT * FROM #__users");
if($db->loadCSVFile('users')){
    echo "Export Complete<br />\n";
} else {
    echo "Error<br />\n";
}
?>
