<?php

/**
* Association
*/
class Association
{
	const TABLE = "association";

	private $data = array("id" => null, "name" => null, "description" => null, 
		"date_added" => null, "added_by" => null, "removed" => null, 
		"removed_by" => null, "reason_removed" => null, "date_removed" => null);

	public function __get($field){
		if(!array_key_exists($field, $this->data)){
			throw new Exception($field." is not a member of ".get_class());
		}
		return $this->data[$field];
	}

	public function __set($field, $value){
		if(!array_key_exists($field, $this->data)){
			throw new Exception($field." is not a member of ".get_class());
		}
		$this->data[$field] = $value;
	}

	public function save(){
		$query = "insert into ".Association::TABLE." (name, description, date_added,".
			" added_by) values('$this->name', '$this->description', '".date("d-m-Y").
			"', $this->added_by);";

		$conn = Db::get_connection();
		return $conn->exec($query);
	}

	public function update(){
		$query = "update ".Association::TABLE." set name = '$this->name', description =".
			" '$this->description' where id = $this->id";
		$conn = Db::get_connection();
		return $conn->exec($query);
	}

	public function delete(){
		$query = "update ".Association::TABLE." set removed = 1, removed_by = ".
			"$this->removed_by, reason_removed = '$this->reason_removed', ".
			"date_removed = '".date("d-m-Y")."' where id = $this->id";
		$conn = Db::get_connection();
		return $conn->exec($query);
	}

	private static function returnAssociationFromResource($resource){
		return returnObjectFromResource(get_class(), $resource, 
			array("id", "name", "description", "date_added", "added_by", "removed", "removed_by", 
				"reason_removed", "date_removed"));
	}

	public static function listAssociations($args = array("index" => 0, "limit" => 100, 
		"removed" => 0)){
		
		$query = "select * from ".Association::TABLE;
		$query = queryBuilder($query, $args);
		$conn = Db::get_connection();
		$associations = array();
		foreach ($conn->query($query) as $row) {
			array_push($associations, Association::returnAssociationFromResource($row));
		}
		return $associations;
	}

	public static function findAssociation($id){
		$conn = Db::get_connection();
		$query = "select * from ".Association::TABLE." where id = $id limit 1";
		$result = null;
		foreach ($conn->query($query) as $row) {
			$result = Association::returnAssociationFromResource($row);
		}
		return $result;
	}

	public static function toJson($args){
		return ToJson(get_class(), $args, array("id", "name", "description", "date_added", 
			"added_by", "removed", "removed_by", "reason_removed", "date_removed"));
	}

	public static function countAssociation($removed = 0){
		$query = "select count(id) as row_count from ".Association::TABLE.
			" where removed = $removed limit 1";
		$count = 0;
		$conn = Db::get_connection();
		foreach ($conn->query($query) as $row) {
			$count = $row['row_count'];
		}
		return $count;
	}

	public static function getFilters($args = array('id', 'name', 'description'), 
		$filters = array('removed' => 0)){
		return getFilters(Association::TABLE, $args, $filters);
	}
}