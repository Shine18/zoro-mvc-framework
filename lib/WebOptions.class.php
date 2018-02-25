<?php
/**
* WebOptions to store options for website in database
* Needs a connection to sql when initializing
*/
class WebOptionsLib
{
	var $conn;
    var $table_name;
	function __construct($tname="")
	{
        $this->table_name = "wo_options";
        $this->construct_table();
	}
	function construct_table(){
        $query = "CREATE TABLE IF NOT EXISTS " . $this->table_name . " (
            wo_key VARCHAR(300) NOT NULL,
            wo_value TEXT NULL
        );";
        SQLLib::raw_query($query);
	}
    function save_option($name,$value){
        $check_query = "SELECT * FROM $this->table_name WHERE wo_key='$name'";
        SQLLib::raw_query($check_query);
        if( SQLLib::$affected_rows > 0 ){
            SQLLib::update_where($this->table_name,"wo_value",$value,"wo_key",$name);
        }
        else{
            SQLLib::insert($this->table_name,array('wo_key',"wo_value"),array($name,$value));
        }
    }
    function get_option($key){
        $res = SQLLib::select($this->table_name,"*",array("wo_key"),array("wo_key" => $key));
        return $res[0]["wo_value"];
    }
}
?>