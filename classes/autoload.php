<?php
    $pathlocal = dirname(__FILE__);
    require_once(dirname($pathlocal)."/functions.php");
    function __autoload($classe){
    	global $pathlocal;
        $classe = str_replace('..', '', $classe);
        require_once(dirname(@$pathlocal)."/$classe.class.php");
    }
?>
