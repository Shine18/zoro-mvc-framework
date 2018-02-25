<?php 
	class WebController{
		public static $layout = "application";
		public static function layout(){
			return static::$layout;
		}
		public static function get_layout($custom_layout = "",$args=[]){
			$layout = ( empty($custom_layout) ? static::layout() : $custom_layout);
			if( file_exists(LAYOUTS_PATH . "$layout.php")){
				global $options;
				ob_start();
				extract($args);
				$return = include(LAYOUTS_PATH . "$layout.php");
				$output = ob_get_clean();
				return $output;
			}
			return "";
		}
		public static function render($path='',$args=[]){
			global $options;
			$layout = null;
			if(isset($args["layout"])){
				$layout = $args["layout"];
			}
			$controller = str_replace("controller","",strtolower(get_called_class()));
			$view = "";
			$layout_output = static::get_layout($layout,$args);

			if(!empty($path)){
				if( file_exists(VIEWS_PATH . "$path.php")){
					ob_start();
					extract($args);
					$return = include(VIEWS_PATH . "$path.php");
					$view = ob_get_clean();
				}
			}
			else{
				$caller = debug_backtrace()[1]["function"];
				$view_file = VIEWS_PATH. "$controller/$caller.php";
				if( file_exists($view_file)){
					ob_start();
					extract($args);
					$return = include("$view_file");
					$view = ob_get_clean();
				}
			}
			echo str_replace("[@content]", $view,$layout_output );
		}

		public static function files($route=""){
			
		}
		public static function error_404(){
			static::render("404");
		}
	}

?>
