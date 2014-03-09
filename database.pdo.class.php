<?php
/**
 * Clase que actua como capa de enlace con la base de datos, basada en PDO.
 * @author David Unay Santisteban <slavepens@gmail.com>
 * @package SlaveFramework
 * @copyright (c) 2014, David Unay Santisteban
 * @version 2.6.20140306
 */
 
class database {
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
     * Contructor de la clase de abstraccion, solo es accesible desde el metodo
     * estatico getInstance().
     */
    private function __construct() {
        $config = new Config();
        try {
            $this->instance = new PDO($config->driver.':host='.$config->dbhost.';dbname='.$config->dbname, $config->dbuser, $config->dbpass);
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
    public static function getInstance() {
        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }
        return self::$instance;
    }
    
    /**
     * Ejecuta una sentencia SQL.
     * @param string $sql sentencia SQL a ejecutar.
     */
    public function query($sql,$params = null) {
        $this->stmt = $this->instance->prepare($sql);
        $this->stmt->execute($params);
        $this->checkQuery();
    }
    
    /**
     * Transforma el resultado de una consulta en un objeto.
     * @param string $class_name nombre de la clase del objeto.
     * @return object objetos final
     */
    public function loadObject($class_name = "stdClass"){
        $object = $this->stmt->fetchObject($class_name);
        return $object;
    }
    
    /**
     * Devuelve una lista de objetos a partir de una consulta 
     * de multiples resultados.
     * @param string $class_name nombre de la clase del objeto.
     * @return array lista de objetos.
     */
    public function loadObjectList($class_name = "stdClass"){
        $objectList = array();
        while($object = $this->stmt->fetchObject($class_name)){
            $objectList[] = $object;
        }
        return $objectList;
    }
    
    /**
     * Trasforma el resultado de UNA SOLA LINEA en una array asociada php.
     * @return array 
     */
    public function loadAssocRow(){
        $assocRow = $this->stmt->fetch(PDO::FETCH_ASSOC);
        return $assocRow;
    }
    
    /**
     * Transforma la matriz de resultado MySQL en una matriz asociada php.
     * @return array matriz php de datos.
     */
    public function loadAssocList(){
        $assocList = array();
        while($row = $this->stmt->fetch(PDO::FETCH_ASSOC)){
            $assocList[] = $row;
        }
        return $assocList;
    }
    
    /**
     * Trasforma el resultado de UNA SOLA LINEA en una array indexada php.
     * @return array 
     */
    public function loadIndexedRow(){
        $assocRow = $this->stmt->fetch(PDO::FETCH_NUM);
        return $assocRow;
    }
    
    /**
     * Transforma la matriz de resultado MySQL en una matriz indexada php.
     * @return array matriz php de datos.
     */
    public function loadIndexedList(){
        $assocList = array();
        while($row = $this->stmt->fetch(PDO::FETCH_NUM)){
            $assocList[] = $row;
        }
        return $assocList;
    }
    
    /**
     * Inicia una transaccion.
     */
    public function startTransaction(){
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
    public function endTransaction(){
       if ($this->sentinel == 1) {
           $this->instance->commit();
       } else {
           $this->instance->rollBack();
       }
       return $this->sentinel;
    }
    
    /**
     * Comprueba si la consulta se a realizado de manera correcta o no.
     */
    private function checkQuery(){
        $this->error = $this->stmt->errorInfo();
        if ($this->error[0] != 00000) {
            $this->sentinel = 0;
        }
        // simpre que se realiza un SELECT esto pone $sentinel a 1
        $this->affectedRows = $this->stmt->rowCount();
        if ($this->affectedRows == 0){
            $this->sentinel = 0;
        }
    }
    
    /**
     * Devuleve el numero de filas afectadas en la consulta.
     * @return int numero de filas afectadas.
     */
    public function getAffectedRows(){
        return $this->affectedRows;
    }
    
    /**
     * Devuleve el ultimo error producido en la conexion con la base de datos
     * @return array datos del error ['code'] y ['desc'].
     */
    public function getError(){
        $e = array();
        $e['ref'] = $this->error[0];
        $e['code'] = $this->error[1];
        $e['desc'] = $this->error[2];
        return $e;
    }
}
?>
