<?php 

class CookieLib{
	const LIMIT_ACCESS = false; //If true, blocks JS cookie API access by default (can be overridden case by case)
	const PREFIX = ''; //In some cases you may desire to prefix your cookies

	public static function get($Key) {
		if (!isset($_COOKIE[SELF::PREFIX.$Key])) {
			return false;
		}
		return $_COOKIE[SELF::PREFIX.$Key];
	}
	//Pass the 4th optional param as false to allow JS access to the cookie
	public static function set($Key, $Value, $Seconds = 86400, $LimitAccess = SELF::LIMIT_ACCESS) {
		setcookie(SELF::PREFIX.$Key, $Value, time() + $Seconds, '/', null, false,$LimitAccess);
	}
	public static function delete($Key) {
		setcookie(SELF::PREFIX.$Key, '', time() - 24 * 3600); //3600 vs 1 second to account for potential clock desyncs
	}
	public static function flush() {
		$Cookies = array_keys($_COOKIE);
		foreach ($Cookies as $Cookie) {
			$this->del($Cookie);
		}
	}
}
 ?>