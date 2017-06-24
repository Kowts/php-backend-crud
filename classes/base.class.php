<?php
    require_once (dirname(__FILE__).'/autoload.php');
    protectFile(basename(__FILE__));

    abstract class base extends database{
        //proriedades
        public $table = "";
        public $fields_values = array();
        public $fieldpk = NULL;
        public $valuepk = NULL;
        public $extra_select = "";

        //method
        public function addField($field=NULL, $value=NULL) {
            if($field != NULL){
                $this->fields_values[$field] = $value;
            }
        }//addField
        public function delField($field=NULL) {
            if (array_key_exists($field, $this->fields_values)) {
                unset($this->fields_values[$field]);
            }
        }//delField
        public function setValue($field=NULL, $value=NULL) {
            if ($field != NULL && $value != NULL) {
                $this->fields_values[$field] = $value;
            }
        }//setValue
        public function getValue($field=NULL) {
            if ($field!=NULL && array_key_exists($field, $this->fields_values)) {
                return $this->fields_values[$field];
            } else {
                return FALSE;
            }
        }//getValue

    }//end

?>

