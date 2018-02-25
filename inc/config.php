<?php 
define("CONTROLLER_PATH", "controllers/");
define("MODEL_PATH","models/");

define("VIEWS_PATH","views/");
define("LAYOUTS_PATH",VIEWS_PATH . "layouts/");
define("PARTIALS_PATH",VIEWS_PATH . "partials/");

define("LIB_PATH","lib/");

define("ASSETS_PATH","assets/");
define("JS_ASSETS_PATH",ASSETS_PATH . "js/");
define("CSS_ASSETS_PATH",ASSETS_PATH . "css/");

define("PUBLIC_FILES_PATH", "public/files/");
define("PUBLIC_IMAGES_PATH", "public/images/");

define("LOGS_PATH","logs/");
define("SQL_LOG_FILE",LOGS_PATH . "sql.txt");

define("HOST","http://localhost/zoro-framework/"); # Add the domain here, this is mandatory , add it with the specific protocol like http or https e.g ( http://www.xyz.com)

define("SQL_HOST","localhost"); # SQL HOST HERE
define("SQL_USERNAME","root"); # SQL USERNAME HERE
define("SQL_PASSWORD",""); # Your SQL password here
define("SQL_DATABASE","phpa");  # add your database name here


define("MAX_UPLOAD_SIZE",5000000); 
?>