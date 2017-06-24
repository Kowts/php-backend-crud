<?php
   
    require_once (dirname(__FILE__).'/autoload.php');
    protectFile(basename(__FILE__));
    
    class session{
        protected $id;
        protected $nvars;
        
        public function __construct($initiate=TRUE) {
            if ($initiate==TRUE) {
                $this->start();
            }
        }
        
        public function start() {
            @session_start();
            $this->id = session_id();
            $this->setNvars();
        }
        private function setNvars(){
            $this->nvars = sizeof($_SESSION);
        }
        
        public function getNvars(){
            return $this->nvars;
        }
        
        public function setVar($var, $value){
            $_SESSION[$var] = $value;
            $this->setNvars();
        }
        
        public function unsetVar($var) {
            unset($_SESSION[$var]);
            $this->setNvars();
        }
        
        public function getVar($var) {
            if (isset($_SESSION[$var])) {
                return $_SESSION[$var];
            }  else {
                return NULL;
            }
        }
        
        public function destroy($initiate=FALSE) {
            session_unset();
            session_destroy();
            $this->setNvars();
            if($initiate==TRUE){
                $this->start();
            }
        }
        
        public function printAll() {
            foreach ($_SESSION as $k => $v){
                printf("%s = %s<br/>", $k, $v);
            }
        }       

    }
    
?>

