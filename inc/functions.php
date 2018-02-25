<?php 
function getCurrentUri(){
    $basepath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
    $uri = substr($_SERVER['REQUEST_URI'], strlen($basepath));
    if (strstr($uri, '?')) $uri = substr($uri, 0, strpos($uri, '?'));
    $uri = '/' . trim($uri, '/');
    return $uri;
}
function getCurrentRoute(){
    return implode("/",array_values(array_filter(explode("/",getCurrentUri()))));
}
function path($path){
	echo HOST . implode("/",array_values(array_filter(explode("/",$path))));
}
function route_path_with_params($route,$args=array()){
	$route_arr = array_values(array_filter(explode('/', $route) ) );
	$new_arr = array();
	foreach($route_arr as $key=>$val){
		$new_arr[$key] = $val;
		$char_arr = str_split($val);
		if( in_array(":",$char_arr)){
			$param = str_replace(":", "", $val);
			if(isset($args[$param])){
				$new_arr[$key] = $args[$param];
			}
			else{
				$new_arr[$key] = null;
			}
		}
	}
	// if( count($new_arr) > 0){
		return implode("/",array_values(array_filter($new_arr)));
	// }
	// return "/";
	
}
function create_route_path($route,$as){
	if( !isset($GLOBALS['route_paths']) ){
	    $GLOBALS['route_paths'] = array();
	}
	$arr = array_values(array_filter( explode("/",$route)));
	$new_arr = array();
	foreach( $arr as $key=>$val){
	    if( strpos($val,":") == false && !is_integer(strpos($val,":") ) ){
	        array_push($new_arr, $val);
	    }
	}
	$path_name = implode("_",$new_arr) . "_path";
	if( $as != null){
	    $path_name = $as ."_path";
	}
	if(!in_array($path_name, $GLOBALS['route_paths']) ){
		array_push($GLOBALS['route_paths'],$path_name);	
    $fncode = "function $path_name(\$args=array()){ return HOST . route_path_with_params('$route',\$args);};";
    eval($fncode);
	}
}
$GLOBALS["routes"] = array();

function pushRoute($route,$fn, $method="GET", $name=null){
    $r = trim($route,"/");
    $route_arr = array_values(array_filter(explode("/",$r)));
    // if( count($route_arr) > 0){
    	$route_info = array();
    	$route_info["path"] = $route_arr;
    	$route_info["fn"] = $fn;
    	$route_info["method"] = $method;
    	$route_info["as"] = $name;
    	array_push($GLOBALS['routes'],$route_info);
    // }
    return $route_arr;
}
function get($route,$fn,$as = null){
    create_route_path($route,$as);

    $GLOBALS["route_keys"] = array();
    $this_uri = array_values(array_filter(explode("/",getCurrentUri())));
    $this_route = pushRoute($route,$fn,"GET",$as);

    // defining global function for path
    $path_name = implode("_",$this_uri) . "_path";
}
function redirect($route){
    $r = trim($route,"/");
    $route_arr = array_values(array_filter(explode("/",$r)));
    $this_uri = explode("/",getCurrentUri());
    $this_uri = array_values(array_filter($this_uri));
    $f_uri = implode("/",$this_uri);
    $f_route = implode("/",$route_arr);
    if( $f_uri != $f_route){
        header("Location: " . HOST .implode("/",$route_arr));
        return true;
    }
    
}
function post($route,$fn,$as = null){
    create_route_path($route,$as);
    
    $GLOBALS["route_keys"] = array();
    $this_uri = array_values(array_filter(explode("/",getCurrentUri())));
    $this_route = pushRoute($route,$fn, "POST",$as);
}



function create_slug($string){
   $slug=preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
   return strtolower($slug);
}
function upload_image($file){
	$target_file = PUBLIC_IMAGES_PATH . basename($_FILES[$file]["name"]);
	$extension = pathinfo($target_file,PATHINFO_EXTENSION);
	$target_file = str_replace(".$extension", "_".  time() . ".$extension",$target_file);
	$file_type = strtolower($extension);
	if( !getimagesize($_FILES[$file]['tmp_name'])){
		return false;
	}
	if( $_FILES[$file]["size"] > MAX_UPLOAD_SIZE){
		// flash_message("Max Upload file size : " . MAX_UPLOAD_SIZE . "Kb");
		return false;
	}
	if( $file_type != "jpg" && $file_type != "png" && $file_type != "jpeg" && $file_type != "gif" && $file_type != "ico" ){
		// flash_message("Invalid Image file","error");
		// var_dump($_SESSION);
		return false;
	}

	if( move_uploaded_file($_FILES[$file]["tmp_name"], $target_file)){
		return HOST . PUBLIC_IMAGES_PATH . basename($target_file);
	}
	else{
		return false;
	}
}

function delete_image($file){
	$image_path = PUBLIC_IMAGES_PATH . basename($file);
	if (file_exists($image_path)) { unlink ($image_path); }
}
function image_size($file){
	$image_path = PUBLIC_IMAGES_PATH . basename($file);
	if (file_exists($image_path)) { return filesize($image_path); }
	return false;
}
function load_css($name){
	echo "<link rel='stylesheet' href='" . HOST . CSS_ASSETS_PATH. "$name.css' />";
}
function load_js($name){
	echo "<script src='". HOST . JS_ASSETS_PATH. "$name.js'></script>";
}
function load_asset($name){
	echo HOST . ASSETS_PATH . "$name";
}
function flash_message($message="",$msg_type="success"){
	$template = '<div class="alert alert-[type] alert-dismissible fade show" role="alert">
		[error]
	  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
	    <span aria-hidden="true">&times;</span>
	  </button>
	</div>';
	$_SESSION["flash"] = true;
	if( !isset($_SESSION["flash_messages"])){
		$_SESSION["flash_messages"] = array();
	}
	array_push($_SESSION["flash_messages"], str_replace("[type]", $msg_type, str_replace("[error]",$message,$template)));
}
function flush_message(){
	if( isset($_SESSION["flash"]) ){
		if( $_SESSION["flash"] == true){
			foreach($_SESSION["flash_messages"] as $message){
				echo $message;
			}
			$_SESSION['flash'] = false;
			$_SESSION["flash_messages"] = array();
		}
	}
}
function render_partial($args = []){
	ob_start();
	extract($args);
	global $options;
	$return = include(PARTIALS_PATH . "$path.php");
	$output = ob_get_clean();
	echo $output;
}

 ?>