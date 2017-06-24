<?php

	//timezone
	date_default_timezone_set("Atlantic/Cape_Verde");

	//html text
	header('Content-Type: text/html; charset=utf-8');

	//diretoris do sistema
	define("BASEPATH", dirname(__FILE__) . "/");
	define("BASEURL", "http://link-to-your-project/");
	define("CLASSESPATH", "classes/");
	define("MODULESPATH", "modules/");
	define("CSSPATH", "assets/css/");
	define("JSPATH", "assets/js/");

	//app enviroment
	define('ENVIRONMENT', "development");

	//data base
	define("DBHOST", "localhost");
	define("DBUSER", "root");
	define("DBPASS", "pass");
	define("DBNAME", "dbname");

	//enviroment options
	if (defined('ENVIRONMENT')) {
		switch (ENVIRONMENT) {
			case 'development':
				ini_set("error_reporting", E_ALL ^ E_DEPRECATED | E_NOTICE);
				break;
			case 'production':
				error_reporting(0);
				break;
			default:
				exit('The application environment is not set correctly. Contact the administrator.');
		}
	}
?>

