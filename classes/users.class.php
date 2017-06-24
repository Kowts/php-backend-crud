<?php

    require_once (dirname(__FILE__) . '/autoload.php');
    protectFile(basename(__FILE__));

    class users extends base {

        public function __construct($fields = array()) {
            parent::__construct();
            $this->table = "users";
            if (sizeof($fields) <= 0) {
                $this->fields_values = array(
                    "fullname" => NULL,
                    "username" => NULL,
                    "password" => NULL,
                    "email" => NULL,
                );
            } else {
                $this->fields_values = $fields;
            }
            $this->fieldpk = "id_users";
        }

        //construct
        public function doLogin($object) {

            $user = $object->getValue('username');
            $pass = $object->getValue('password');

            $object->extra_select = "WHERE username='" . $user . "' AND password='" . encryptPass($pass) . "' AND active='s'";
            $this->selectAll($object);
            $session = new session();
            if ($this->lines == 1) {
                $usLogin = $object->returnData();
                $session->setVar('idusers', $usLogin->id_users);
                $session->setVar('fullname', $usLogin->fullname);
                $session->setVar('username', $usLogin->username);
                return TRUE;
            } else {
                $session->destroy(TRUE);
                return FALSE;
            }
        }

        //doLogin
        function doLogout() {

        	//user session info
            $session = new session();
            $iduser = $session->getVar('idusers');

            $session->destroy(TRUE);
            redirect('?error=1');
        }

        function existRegister($field = NULL, $value = NULL) {
            if ($field != NULL && $value != NULL) {
                is_numeric($value) ? $value = $value : $value = "'" . $value . "'";
                $this->extra_select = "WHERE $field=$value";
                $this->selectAll($this);
                if ($this->lines > 0) {
                    return TRUE;
                } else {
                    return FALSE;
                }
            } else {
                $this->errors(__FILE__, __FUNCTION__, NULL, 'Faltam parâmetros para executar a função', TRUE);
            }
        }

    }

    //end
?>

