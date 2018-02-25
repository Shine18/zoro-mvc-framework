<?php 
class LoggerLib{
	public static $last_log_time;
	public static $last_log;
	public static $last_log_file;

	public static function log($log,$log_file){
		$t = date("Y-m-d H:i:s");
		$log_to_write = "$t ::  $log\n";
		file_put_contents($log_file, $log_to_write, FILE_APPEND | LOCK_EX);
	}
}

 ?>