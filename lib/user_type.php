<?php

/**
* UserType Class
*/
class UserType
{
	const TABLE = "user_type";

	private $data = array("id" => null, "name" => null);

	public function __get($field){
		if(!array_key_exists($field, $this->data)){
			throw new Exception($field." is not a valid member of ".get_class());
		}
		return $this->data[$field];
	}

	public function __set($field, $value){
		if(!array_key_exists($field, $this->data)){
			throw new Exception($field." is not a valid member of ".get_class());
		}
		$this->data[$field] = $value;
	}

	public static function toJson($args){
		return ToJson(get_class(), $args, array("id", "name"));
	}

	public static function listUserType(){
		$query = "select * from ".UserType::TABLE;
		$conn = Db::get_connection();
		$usertypes = array();
		foreach ($conn->query($query) as $row) {
			array_push($usertypes, UserType::returnUserTypeFromResource($row));
		}
		return $usertypes;
	}

	private static function returnUserTypeFromResource($resource){
		return returnObjectFromResource(get_class(), $resource, 
			array("id", "name"));
	}
}