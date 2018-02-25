<?php 
session_start();

// Autoloading
spl_autoload_register('load');
// Define a custom load method

function load($classname){
    // Here simply autoload app’s controller and model classes
	if (substr($classname, -10) == "Controller"){
		require_once CONTROLLER_PATH . "$classname.php";
	} elseif (substr($classname, -5) == "Model"){
		require_once  MODEL_PATH . "$classname.php";
	}elseif (substr($classname, -3) == "Lib"){
		require_once  LIB_PATH  . str_replace("Lib", "", $classname). ".class.php";
	}
	else{
		require_once MODEL_PATH . $classname . ".php";
	}
}

SQLLib::config(SQL_HOST,SQL_USERNAME,SQL_PASSWORD,SQL_DATABASE);
// global $options;
$options = new WebOptionsLib(SQLLib::getInstance());



 ?>