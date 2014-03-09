database-pdo-class
==================
Capa de abstracción de acceso a base de datos (PDO)

Configuración
=============

Bien, lo primero que debemos hacer para usar la capa de abstracción, es crear una nueva clase a la que llamaremos Config y que contendrá los parámetros de configuración de la BD, así como otros valores de configuración para nuestra aplicación web.

```php
class Config {
    public $driver = 'mysql';
    public $dbhost = 'localhost';
    public $dbuser = 'usuario_basedatos';
    public $dbpass = 'clave_basedatos';
    public $dbname = 'nombre_basedatos';
}
```

Una vez que hemos configurado los datos de acceso, debemos importar la configuración y la capa de abstracción:

```php
require 'configuration.php';
require 'database.pdo.class.php';
``` 
Uso
===

Ahora ya podemos usar la capa con total libertad, por ejemplo si queremos realizar una consulta a una tabla primero debemos obtener una instancia de la capa de abstracción, luego definir la consulta que deseamos realizar y finalmente seleccionar como queremos obtener los datos:

```php
$db = database::getInstance();
$db->query('SELECT * FROM usuarios');
$usuarios = $db->loadObjectList();
```

En el código de arriba vemos que primero obtenemos una instancia, luego ejecutamos la consulta y finalmente obtenemos los datos en forma de lista de objetos. Esta clase nos permite obtener los datos de las siguientes maneras:

* Objeto unico:loadObject();
* Lista de objetos:loadObjectList();
* Array asociada:loadAssocRow();
* Lista de arrays arrociadas:loadAssocList();
* Array indexado: loadIndexedRow();
* Lista de arrays indexados: loadIndexedList();

Podemos realizar consultas preparadas de forma fácil y sencilla, usando el mismo método query(), al cual le pasaremos la consulta preparada y un array opcional con los parámetros de dicha consulta:

```php
$db = database::getInstance();
$sql = "SELECT * FROM usuarios WHERE id = :id";
$params = array(':id' => 2);
$db->query($sql,$params);
$result = $db->loadObject();
```

Transacciones
=============

También podemos realizar transacciones de una manera muy sencilla usando los métodos startTransaction() y endTransaction() tal y como vemos en el ejemplo:

```php
$db = database::getInstance();
$db->startTransaction();
$db->query("INSERT INTO usuarios VALUES (NULL, 'Tyrande', 'Whisperwind')");
$db->query("INSERT INTO usuarios VALUES (NULL, 'Vincent', 'Vega')");
$db->endTransaction();
```

El método startTransaction() inicia la transacción y activa la escucha de errores para que cuando se ejecute el método endTransaction() se ejecute commit() si todo a ido bien o rollback() a ocurrido algún fallo en las consultas.

Errores
=======

Esta clase también nos permite obtener los posibles errores de cada consulta usando el método getError(), que nos devolverá un array asociativo con el código de error y la descripción de dicho error:

```php
$db = database::getInstance();
$db->query('SELECT * FROM suarios');
$error = $db->getError();
var_dump($error);
```

Al imprimir la variable error veremos:

```php
array (size=2)
  'code' => int 1146
  'desc' => string 'Table 'test.suarios' doesn't exist' (length=34)
```
