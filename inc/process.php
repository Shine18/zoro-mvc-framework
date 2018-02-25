<?php 
	$GLOBALS["route_used"] = false;
	$this_uri = array_values(array_filter(explode("/",getCurrentUri())));

	if( count($GLOBALS["routes"])){
		// creating paths 
		foreach($GLOBALS['routes'] as $key=>$route){
			create_route_path(implode("/",$route["path"]),$route["as"]);
		}

		foreach($GLOBALS['routes'] as $key=>$route){
			$this_route = $route['path'];
			$is_route = true;
			$route_keys = array();

			if($GLOBALS["route_used"] == true){
				break;
			}
			if( $_SERVER['REQUEST_METHOD'] != $route['method']){
			    continue;
			}
			if(count($this_uri) != count($this_route)){
			    $is_route = false;
			}
			foreach($this_uri as $key=>$val){
		    if( isset($this_route[$key])){
		        if( strpos($this_route[$key],":") == false && !is_integer(strpos($this_route[$key],":") ) ){
		            if($this_route[$key] != $val){
		                $is_route = false;
		            }
		        }
		        else{
		            $url_key = str_replace(":", "", $this_route[$key]);
		            $route_keys[$url_key] = $val;
		        }
		    }
		    else{
		        $is_route = false;
		    }
			}

			if( $is_route == true){
				$route["fn"]($route_keys);
				$GLOBALS["route_used"] = true;
			}
		}
	}
	if( $GLOBALS['route_used'] == false){
		if( function_exists("error_404_path")){
			redirect("/404");
		}
		else{
			
		}
	}
 ?>