<?php 
abstract class Model{
	protected $table_name = '';
	protected $sql;
	protected $properties = array();
	protected $in_db = false;
	protected $primary_key_name;
	public function __construct($args){
		$this->sql = SQLLib::getInstance();
		foreach($args as $key=>$value){
			if( $key == "from_db"){
				break;
			}
			if( isset($args["from_db"]) && $args["from_db"] == true){
				$this->set_property($key,$value,false);
			}
			else{
				$this->set_property($key,$value);
			}
		}
	}

	public final function __get($property_or_class_name){
		return $this->get_property($property_or_class_name);
	}
	public final function get_property($property_or_class_name){
		if( $property_or_class_name == "primary_key"){
			return $this->properties[$this->primary_key_name]['value'];
		}
		elseif( array_key_exists($property_or_class_name, $this->properties)){
			return $this->properties[$property_or_class_name]['value'];
		}
		else{
			throw new Exception( get_class($this) . ' property/parent object '. $property_or_class_name . " does not exist");
			return '';
		}
	}
	public final function set_property($property_name,$property_value,$encrypt=true){
		if( array_key_exists($property_name, $this->properties)){
			if( $this->properties[$property_name]['is_protected']){
				throw new Exception("Cannot set value of protected property");
				return false;
			}
			else{
				if( $this->properties[$property_name]['is_encrypted']){
					if( $encrypt){
						$this->properties[$property_name]['value'] = $this->encrypt($property_value);
					}
					else{
						$this->properties[$property_name]['value'] = $property_value;
					}
				}
				else{
					$this->properties[$property_name]['value'] = $property_value;
				}
				
			}
		}
		else{
			throw new Exception( get_class($this) . ' property ' . $property_name . " does not exist");
			return false;
		}
		return true;
	}
	public final function __set($property_name, $property_value){
		return $this->set_property($property_name,$property_value);
	}

	protected final function add_property($property_name, $type, $is_protected = false, $is_primary_key = false, $is_unique=false, $encrypted = false){
		$this->properties[$property_name] = array(
			'value'              => '',
			'type'							 => $type,
			'is_protected'       => $is_protected,
			'is_primary_key'     => $is_primary_key,
			'is_unique'					 => $is_unique,
			'is_encrypted'			 => $encrypted
		);
		if( $is_primary_key == true){
			$this->primary_key_name = $property_name;
		}
		return true;
	}
	public final function verify_unique(){
		foreach($this->properties as $name=>$prop){
			if( $prop["is_unique"]){
				if( static::find_by($name,$prop["value"]) != false ){
					throw new Exception( "Another " . get_class($this) . " with same $name value (". $prop["value"] . ") exists");
					return false;
				}
			}
		}
		return true;
	}
	public final function clone(){
		$args = array();
		foreach($this->properties as $property=>$value){

			if( !$value['is_primary_key'] && !$value['is_unique']){
				$args[$property] = $value['value'];
			}
		}
		// var_dump($args);
		return new static($args);
	}
	public final function save(){
		if( count($this->properties) < 1){
			return false;
		}
		else if( $this->in_db == true){
			return $this->update_in_db();
		}
		else if( !$this->verify_unique() ){
			return false;
		}

		return $this->insert_in_db();
	}
	public final function update_in_db(){
		$db_data = static::find($this->id);
		foreach($this->properties as $name=>$prop){
			if(!$prop["is_primary_key"]){
				if($db_data->get_property($name) != $prop["value"]){
					// echo $db_data->get_property($name);
					SQLLib::update(static::get_table_name(),$this->id,$name,$prop["value"]);
				}
			}
		}
		return true;
	}
	public final function update($args){
		foreach($args as $key=>$value){
			$this->set_property($key,$value);
		}
		return $this->save();
	}
	public final function load(){
		$data = static::find($this->id);
		foreach($this->properties as $name=>$val){
			$this->set_property($name,$data->get_property($name));
		}
	}
	public final function destroy(){
		if( !$this->in_db){
			return false;
		}
		SQLLib::delete(static::get_table_name(),$this->id);
		$this->in_db = false;
		return true;
	}
	public static final function find($id){
		return static::find_by("id",$id);
	}
	public static final function find_by($key,$value){
		$result = SQLLib::find_by(static::get_table_name(),$key,$value);
		$result_arr = array();
		if( count($result) < 1){
			return false;
		}
		else if( ! is_array($result)){
			$result["from_db"] = true;
			$final_obj = new static($result);
		}
		else if( count($result) == 1){
			$result[0]["from_db"] = true;
			$final_obj = new static($result[0]);
		}
		else{
			foreach( $result as $row){
				$row["from_db"] = true;
				$obj = new static($row);
				$obj->in_db = true;
				array_push($result_arr,$obj);
			}
			return $result_arr;
		}

		$final_obj->in_db = true;
		return $final_obj;
	}
	public static final function where($key,$value){
		// $result = SQLLib::select(static::get_table_name(),"*",array($key),array($value));
		$res = static::find_by($key,$value);
		$r = array();
		if( !is_array($res)){
			$r[0] = $res;
		}
		else{
			$r = $res;
		}
		return $r;
	}
	public static final function all(){
		$result = SQLLib::select(static::get_table_name(),"*");
		$result_arr = array();

		if( count($result) < 1 || $result == false){
			return array();
		}
		else if( ! is_array($result)){
			$result["from_db"] = true;
			$final_obj[0] = new static($result);
		}
		else if( count($result) == 1){
			$result[0]["from_db"] = true;
			$final_obj[0] = new static($result[0]);
		}
		else{
			foreach( $result as $row){
				$row["from_db"] = true;
				$obj = new static($row);
				$obj->in_db = true;
				array_push($result_arr,$obj);
			}
			return $result_arr;
		}

		$final_obj[0]->in_db = true;
		return $final_obj;
	}

	public final function insert_in_db(){
		$types_string = "";
		$values_arr = array();
		$values_stmt = "VALUES (";
		$save_stmt = "INSERT INTO " . $this->get_table_name() . " (";
		$i = 0;
		foreach($this->properties as $key=>$property){
			if( $i != 0){
				$save_stmt .= ",";
				$values_stmt .= ",";
			}
			$save_stmt .= "$key";
			$values_stmt .= ":$key";
			
			if( $property["is_primary_key"] == true){
				$values_arr[":$key"] = null;
			}
			else{
				$values_arr[":$key"] = $property["value"];
			}
			$i++;
		}
		$values_stmt .= ")";
		$save_stmt .= ") $values_stmt";

		$res = $this->sql->prepare($save_stmt);
		$res->execute($values_arr);	
		$this->id = $this->sql->lastInsertId();
		$this->load();
		$this->in_db = true;
		return true;
	}
	public function encrypt($val){
		return md5($val);
	}
	public static final function get_table_name(){
		$name = get_called_class();
		$last_two = strtolower(substr($name, -2));
		$last_one = strtolower(substr($name, -1));
		if( $last_two == "ch" || $last_two == "sh"){
			$name .= "es";
		}
		elseif ( $last_one == "s" || $last_one == "x" || $last_two == "z") {
			$name .= "es";
		}
		else{
			$name .= "s";
		}
		return strtolower($name);
	}
	public final function set_table_name($tname){
		$this->table_name = $tname;
	}



	protected final function get_primary_key_name(){
		if( $this->primary_key_name = ""){
			return $this->get_table_name() . "_id";
		}
		else{
			return $this->primary_key_name;
		}
	}

}




?>