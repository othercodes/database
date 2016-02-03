<?php



/**
 * Clase que actua como capa de enlace con la base de datos, basada en PDO.
 * @author David Unay Santisteban <slavepens@gmail.com>
 * @package SlaveFramework
 * @copyright (c) 2014, David Unay Santisteban
 * @version 2.9.20140408
 */
class Database
{

    private $config;
    /**
     * Objeto de conexion PDO.
     * @var Object
     */
    private static $instance;

    /**
     * Objeto de conexion para consultas preparadas.
     * @var Object
     */
    private $stmt;

    /**
     * Array los datos de los errores.
     * @var array
     */
    private $error = array();

    /**
     * Variable centinela para transacciones. 1=OK / 0=FAIL
     * @var int
     */
    private $sentinel = 1;

    /**
     * Filas afectadas por la consulta.
     * @var int
     */
    private $affectedRows = 0;

    /**
     * Numero de filas de una consulta SELECT.
     * @var int
     */
    private $countRows = 0;

    /**
     * Contructor de la clase de abstraccion, solo es accesible desde el metodo
     * estatico getInstance().
     */
    private function __construct()
    {
        $this->config = new Config();
        try {
            $this->instance = new PDO($this->config->driver . ':host=' . $this->config->dbhost . ';dbname=' . $this->config->dbname, $this->config->dbuser, $this->config->dbpass);
            return $this->instance;
        } catch (Exception $e) {
            echo "Se ha producido un error en la conexion con la BD.";
        }
    }

    /**
     * Obtienen una instancia nueva de la conexion de la BD o devuelve el id
     * de una ya creada si es que existe.
     * @return id de instancia de conexion MySQL.
     */
    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * Ejecuta una sentencia SQL.
     * @param string $sql sentencia SQL a ejecutar.
     */
    public function query($sql, $params = null)
    {
        if (isset($this->config->prefix)) {
            $sql = str_replace('#__', $this->config->prefix, $sql);
        }
        $this->stmt = $this->instance->prepare($sql);
        $this->stmt->execute($params);
        $this->checkQuery();
    }

    /**
     * Carga un unico resultado(campo).
     * @return type
     */
    public function loadResult()
    {
        $singleResult = $this->stmt->fetch(PDO::FETCH_NUM);
        return $singleResult[0];
    }

    /**
     * Carga los valores de una sola columna.
     * @return array
     */
    public function loadColumn()
    {
        $columnList = array();
        while ($row = $this->stmt->fetch(PDO::FETCH_NUM)) {
            $columnList[] = $row[0];
        }
        $this->countRows = count($columnList);
        return $columnList;
    }

    /**
     * Transforma el resultado de una consulta en un objeto.
     * @param string $class_name nombre de la clase del objeto.
     * @return object objetos final
     */
    public function loadObject($class_name = "stdClass")
    {
        $object = $this->stmt->fetchObject($class_name);
        return $object;
    }

    /**
     * Devuelve una lista de objetos a partir de una consulta
     * de multiples resultados.
     * @param string $class_name nombre de la clase del objeto.
     * @return array lista de objetos.
     */
    public function loadObjectList($class_name = "stdClass")
    {
        $objectList = array();
        while ($object = $this->stmt->fetchObject($class_name)) {
            $objectList[] = $object;
        }
        $this->countRows = count($objectList);
        return $objectList;
    }

    /**
     * Trasforma el resultado de UNA SOLA LINEA en una array asociada php.
     * @return array
     */
    public function loadAssocRow()
    {
        $assocRow = $this->stmt->fetch(PDO::FETCH_ASSOC);
        return $assocRow;
    }

    /**
     * Transforma la matriz de resultado MySQL en una matriz asociada php.
     * @return array matriz php de datos.
     */
    public function loadAssocList()
    {
        $assocList = array();
        while ($row = $this->stmt->fetch(PDO::FETCH_ASSOC)) {
            $assocList[] = $row;
        }
        $this->countRows = count($assocList);
        return $assocList;
    }

    /**
     * Trasforma el resultado de UNA SOLA LINEA en una array indexada php.
     * @return array
     */
    public function loadIndexedRow()
    {
        $indexedRow = $this->stmt->fetch(PDO::FETCH_NUM);
        return $indexedRow;
    }

    /**
     * Transforma la matriz de resultado MySQL en una matriz indexada php.
     * @return array matriz php de datos.
     */
    public function loadIndexedList()
    {
        $indexedList = array();
        while ($row = $this->stmt->fetch(PDO::FETCH_NUM)) {
            $indexedList[] = $row;
        }
        $this->countRows = count($indexedList);
        return $indexedList;
    }

    /**
     * Devuleve el resultado en notacion JSON.
     * @return string
     */
    public function loadJsonObjectList()
    {
        return json_encode($this->loadAssocList());
    }

    /**
     * Devuelve el resultado en fomrato XML.
     * @param string $version version del documento XML.
     * @param string $encoding encodeo del documento XML.
     * @param string $root elemento padre del documento.
     * @param string $elementName nombre de cada nodo hijo.
     * @return string
     */
    public function loadXmlDocument($file = null, $root = 'query', $elementName = 'entry')
    {
        $xml = new DOMDocument('1.0', 'utf-8');
        $table = $xml->createElement($root);
        foreach ($this->loadAssocList() as $entry) {
            $element = $xml->createElement($elementName);
            foreach ($entry as $node => $value) {
                if ($this->valideXmlValue($value)) {
                    $field = $xml->createElement($node, $value);
                    $element->appendChild($field);
                } else {
                    $field = $xml->createElement($node);
                    $cdata = $xml->createCDATASection($value);
                    $field->appendChild($cdata);
                    $element->appendChild($field);
                }
            }
            $table->appendChild($element);
        }
        $xml->appendChild($table);
        if ($file != null) {
            return file_put_contents($file, $xml->saveXML());
        } else {
            return $xml->saveXML();
        }
    }


    public function loadCSVFile($file = null)
    {
        if ($file == null) {
            $file = date('Ymd-His');
        }
        $writer = fopen($file . '.csv', 'w');
        $temp = $this->loadIndexedList();
        foreach ($temp as $row) {
            fputcsv($writer, $row);
        }
        return fclose($writer);
    }

    /**
     * Inicia una transaccion.
     */
    public function startTransaction()
    {
        $this->instance->beginTransaction();
        $this->sentinel = 1;
        $this->affectedRows = 0;
    }

    /**
     * Verifica si las transacciones se han realizado de forma correcta, en
     * ese caso se confirma la escritura en BD, en caso contrario se realiza
     * un rollback. Este metodo verifica UPDATE, INSERT y DELETE.
     * IMPORTANTE: NO USAR UN SELECT EN LA TRANSACCION.
     * @return int devuelve 1 para el commit o 0 para el rollback
     */
    public function endTransaction()
    {
        if ($this->sentinel == 1) {
            $this->instance->commit();
        } else {
            $this->instance->rollBack();
        }
        return $this->sentinel;
    }

    /**
     * Devuleve el numero de filas afectadas en la consulta.
     * @return int numero de filas afectadas.
     */
    public function getAffectedRows()
    {
        return $this->affectedRows;
    }

    /**
     * Devuelve el contador de filas.
     * @return int
     */
    public function getCountRows()
    {
        return $this->countRows;
    }

    /**
     * Obtiene el ultimo id insertado en la consulta anterior
     * @return int
     */
    public function getLastId()
    {
        return $this->instance->lastInsertId();
    }

    /**
     * Devuleve el ultimo error producido en la conexion con la base de datos
     * @return array datos del error ['code'] y ['desc'].
     */
    public function getError()
    {
        $e = array();
        $e['ref'] = $this->error[0];
        $e['code'] = $this->error[1];
        $e['desc'] = $this->error[2];
        return $e;
    }

    /**
     * Comprueba si la consulta se a realizado de manera correcta o no.
     */
    private function checkQuery()
    {
        $this->error = $this->stmt->errorInfo();
        if ($this->error[0] != 00000) {
            $this->sentinel = 0;
        }
        // simpre que se realiza un SELECT esto pone $sentinel a 1
        $this->affectedRows = $this->stmt->rowCount();
    }

    /**
     * Comprueba si existen caracteres ilegales en el valor del campo
     * para marcarlo como elemento CDATA.
     * @param mixed $value
     * @return boolean
     */
    private function valideXmlValue($value)
    {
        $chars = array('<', '>', '&');
        foreach ($chars as $ilegal) {
            $state = strpos($value, $ilegal);
            if ($state !== FALSE) {
                return FALSE;
            }
        }
        return TRUE;
    }
}

?>
