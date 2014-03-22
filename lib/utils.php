<?php

function queryBuilder($queryFragment, $args = array("and" => false)){
	if(array_key_exists("removed", $args) && isset($args['removed'])){
		if($args["and"]){
			$queryFragment .= " and removed = ".$args['removed'];
		}else{
			$queryFragment .= " where removed = ".$args['removed'];
		}
	}

	$queryFragment .= " order by id desc";

	$queryFragment .= check_list_limits($args);
	return $queryFragment;
}

/*
* This method is responsible for adding the offset and limit of the 
* rows to be returned from the database
*/
function check_list_limits($args = array()){
	$limit = "";
	if(array_key_exists("limit", $args) && $args["limit"] != 0){
		if(!array_key_exists("index", $args)){
			$args["index"] = 0;
		}
		$limit = " limit ".$args["index"].", ".$args["limit"];
	}
	return $limit;
}

/*
* This method is responsible for converting the properties of an object
* or array of objects in to an array of array of attributes and value pairs
* of the objects
*/
function ToJson($class, $args = array(), $properties = array()){
	$result = null;
	if(is_array($args)){
		$length_of_array = count($args);
		$instances_found_count = 0;
		foreach ($args as $arg) {
			if($arg instanceof $class){
				$instances_found_count ++;
			}
		}
		if($instances_found_count == $length_of_array){
			$result = array();
			foreach ($args as $arg) {
				$tmp = array();
				foreach ($properties as $key) {
					$tmp[$key] = $arg->$key;
				}
				array_push($result, $tmp);
			}
		}
	}
	if($args != null && !is_array($args) && $args instanceof $class){
		$result = array();
		foreach ($properties as $key) {
			$result[$key] = $args->$key;
		}
	}

	return $result;
}

/*
* This method is responsible for creating an object of the specified class
* when it is passed a resource and the list of the properties of the class
*/
function returnObjectFromResource($class, $resource, $fieldList = array()){
	$object = new $class();
	foreach ($fieldList as $field) {
		$object->$field = $resource[$field];
	}
	return $object;
}