<?php
    
    require_once (dirname(__FILE__).'/autoload.php');
    protectFile(basename(__FILE__));
    abstract class database {

        //propriedades
        public $server = DBHOST;
        public $user = DBUSER;
        public $pass = DBPASS;
        public $dbname = DBNAME;
        public $conn = NULL;
        public $dataset = NULL;
        public $lines = -1;

        //method
        public function __construct() {
            $this->connect();
        }//construct
        public function __destruct() {
            if ($this->conn != NULL) {
                mysql_close($this->conn);
            }
        }//destruct
        public function connect() {
            $this->conn = mysql_connect($this->server, $this->user, $this->pass, TRUE) or die($this->errors(__FILE__, __FUNCTION__, mysql_errno(), mysql_error(), TRUE));
            mysql_select_db($this->dbname) or die($this->errors(__FILE__, __FUNCTION__, mysql_errno(), mysql_error(), TRUE));
            mysql_set_charset('utf8',$this->conn);
            mysql_query("SET NAMES 'utf-8',$this->conn");
            mysql_query("SET character_set_conn=utf-8",$this->conn);
            mysql_query("SET character_set_client=utf-8",$this->conn);
            mysql_query("SET character_set_results=utf-8",$this->conn);
        }//connect
        public function insert($object) {
            $sql = "INSERT INTO ".$object->table." (";
            for ($i=0; $i<count($object->fields_values); $i++){
                $sql .= key($object->fields_values);
                if ($i < (count($object->fields_values)-1)) {
                    $sql .= ", ";
                }  else {
                    $sql .= ") ";
                }
                next($object->fields_values);
            }
            reset($object->fields_values);
            $sql .= "VALUES (";
            for ($i=0; $i<count($object->fields_values); $i++){
                $sql .= is_numeric($object->fields_values[key($object->fields_values)]) ? 
                    $object->fields_values[key($object->fields_values)] :
                    "'".$object->fields_values[key($object->fields_values)]."'";
                if ($i < (count($object->fields_values)-1)) {
                    $sql .= ", ";
                }  else {
                    $sql .= ") ";
                }
                next($object->fields_values);
            }
            return $this->executeSQL($sql);
        }//insert
        public function update($object) {
            $sql = "UPDATE ".$object->table." SET ";
            for ($i=0; $i < count($object->fields_values); $i++){
                $sql .= key($object->fields_values)."=";
                $sql .= is_numeric($object->fields_values[key($object->fields_values)]) ? 
                    $object->fields_values[key($object->fields_values)] :
                    "'".$object->fields_values[key($object->fields_values)]."'";
                if ($i < (count($object->fields_values)-1)) {
                    $sql .= ", ";
                }  else {
                    $sql .= " ";
                }
                next($object->fields_values);
            }
            $sql .= "WHERE ".$object->fieldpk."=";
            $sql .= is_numeric($object->valuepk) ? $object->valuepk : "'".$object->valuepk."'";
            return $this->executeSQL($sql);
        }//update
        public function delete($object) {
            $sql = "DELETE FROM ".$object->table;
            $sql .= " WHERE ".$object->fieldpk."=";
            $sql .= is_numeric($object->valuepk) ? $object->valuepk : "'".$object->valuepk."'";
            return $this->executeSQL($sql);
        }//delete
        function selectAll($object) {
            $sql = "SELECT * FROM ".$object->table;
            if ($object->extra_select!=NULL) {
                $sql .= " ".$object->extra_select;
            }
            return $this->executeSQL($sql);
        }//selectAll
        public function selectFields($object) {
            $sql = "SELECT ";
            for ($i=0; $i<count($object->fields_values); $i++){
                $sql .= key($object->fields_values);
                if ($i < (count($object->fields_values)-1)) {
                    $sql .= ", ";
                }  else {
                    $sql .= " ";
                }
                next($object->fields_values);
            }
            $sql .= " FROM ".$object->table;
            if ($object->extra_select!=NULL) {
                $sql .= " ".$object->extra_select;
            }
            return $this->executeSQL($sql);
        }
        public function executeSQL($sql=NULL) {
            if ($sql!=NULL) {
                $query = mysql_query($sql) or die($this->errors(__FILE__, __FUNCTION__,mysql_errno(), mysql_error(), TRUE));
                $this->lines = mysql_affected_rows($this->conn);
                if (substr(trim(strtolower($sql)),0,6)=='select') {
                    $this->dataset = $query;
                    return $query;
                }  else {
                    return $this->lines;
                }
                
            }  else {
                $this->errors(__FILE__,__FUNCTION__,NULL,'Comando SQL nao formado na rotina!', FALSE);
            }
        }//executeSQL
        public function returnData($type=NULL) {
            switch (strtolower($type)){
                case "array":
                    return mysql_fetch_array($this->dataset);
                    break;
                case "assoc":
                    return mysql_fetch_assoc($this->dataset);
                    break;
                case "object":
                    return mysql_fetch_object($this->dataset);
                    break;
                default:
                    return mysql_fetch_object($this->dataset);
                    break;
            }
        }//returnData
        public function errors($file = NULL, $routine = NULL, $number = NULL, $message = NULL, $exception = FALSE) {
            if ($file == NULL) {
                $file = "Não informado";
            }
            if ($routine == NULL) {
                $routine = "Não informado";
            }
            if ($number == NULL) {
                $number = mysql_errno($this->conn);
            }
            if ($message == NULL) {
                $number = mysql_errno($this->conn);
            }
            $result = 'Ocorreu um erro com os seguintes detalhes:<br>
                <strong>Arquivo:</strong> ' . $file . '<br>
                <strong>Rotina:</strong> ' . $routine . '<br>
                <strong>Codigo:</strong> ' . $number . '<br>
                <strong>Mensagem:</strong> ' . $message;
            if ($exception == NULL) {
                echo($result);
            } else {
                die($result);
            }
        }//errors
    }//end
?>