<?php 
/**
* 
*/
class SQLLib
{
	private static $conn;
	private static $host;
	private static $user;
	private static $password;

	public static $db;

	public static $last_query;
	public static $last_stmt;
	public static $affected_rows;

	public static function config($host="localhost",$user="root",$password="",$db=""){
		static::$host = $host;
		static::$user = $user;
		static::$password = $password;
		static::$db = $db;
	}

	public static function getInstance(){
		$dsn = "mysql:host=" . static::$host . ";dbname=" . static::$db;
		$username = static::$user;
		$password = static::$password;
		$options = array(
		    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
		    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		    PDO::ATTR_PERSISTENT => true
		); 

		if( empty(static::$conn)){
			static::$conn = new PDO($dsn, $username, $password, $options);
		}
		else{
			LoggerLib::log("SQL Connection Start",SQL_LOG_FILE);
		}
		return static::$conn;
	}

	public static function raw_query($query){
		static::getInstance();
		$stmt = static::$conn->prepare($query);
		$res = $stmt->execute();
		static::$last_query = $query;
		static::$last_stmt = $stmt;
		static::$affected_rows = $stmt->rowCount();
		if( static::$affected_rows > 0){
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
		else{
			return true;
		}
	}
	public static function select($table_name,$columns,$where=null,$args=null,$query_order="DESC" ){
		static::getInstance();
		$c = (is_array($columns) ? implode(",",$columns) : $columns);
		$query = "SELECT $c FROM $table_name";
		$where_arr = array();
		if( !empty($where)){
			$query .= " WHERE ";
			$i=0;
			foreach($where as $key){
				if($i!=0){
					$query.=" AND ";
				}
				$query.= "$key=:$key ";
				array_push($where_arr,$key);
				$i++;
			}
		}
		if( $query_order != null){
			$query .= " ORDER BY id $query_order";
		}
		// var_dump($query);
		$stmt = static::$conn->prepare($query);
		foreach($where_arr as $key){
			$stmt->bindParam(":$key",$args[$key],PDO::PARAM_STR);
		}
		// if( $query_order != null){
		// 	$stmt->bindParam(":query_order",$query_order,PDO::PARAM_STR);
		// }
		$stmt->execute();
		static::$affected_rows = $stmt->rowCount();
		if( static::$affected_rows > 0){
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
		else{
			return false;
		}
	}
	public static function insert($table_name,$columns,$values){
		$query = "INSERT INTO $table_name(" . implode(",", $columns) . ") VALUES(";
		$i = 0 ;
		foreach($values as $val){
			if($i != 0){
				$query .= ",";
			}
			if($val != null){
				$query .= "\"$val\"";
			}
			else{
				$query .= "null";
			}
			$i++;
		}
		$query .= ")";
		static::getInstance();
		$stmt = static::$conn->prepare($query);
		$res = $stmt->execute();
		return true;
	}

	public static function find_by($table_name,$key,$val){
		static::getInstance();
		$search_stmt = "SELECT * FROM $table_name WHERE $key=:val";
		$s = static::$conn->prepare($search_stmt);
		$s->execute(array(
			":val" => $val
		));
		$s->setFetchMode(PDO::FETCH_ASSOC);
		return $s->fetchAll();
	}
	public static function update($table_name,$id,$col,$val){
		static::getInstance();
		$update_stmt = "UPDATE $table_name SET $col=:val WHERE id=$id";
		$s = static::$conn->prepare($update_stmt);
		$s->bindParam(":val",$val,PDO::PARAM_STR);
		$s->execute();
		return true;
	}
	public static function update_where($table_name,$col,$val,$col_where,$val_where){
		static::getInstance();
		$update_stmt = "UPDATE $table_name SET $col=:val WHERE $col_where='$val_where'";
		$s = static::$conn->prepare($update_stmt);
		$s->bindParam(":val",$val,PDO::PARAM_STR);
		$s->execute();
		if($s->rowCount() > 0){
			return true;
		}
		return false;
	}
	public static function delete($table_name,$id){
		static::getInstance();
		$delete_stmt = "DELETE FROM $table_name WHERE id=$id";
		$s = static::$conn->prepare($delete_stmt);
		$s->execute();
		return true;
	}

	public static function delete_where($table_name,$col_where,$val_where){
		static::getInstance();
		$delete_stmt = "DELETE FROM $table_name WHERE $col_where='$val_where'";
		$s = static::$conn->prepare($delete_stmt);
		$s->execute();
		return true;
	}
}


?>