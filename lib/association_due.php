<?php

/**
* Association Due
*/
class AssociationDue
{
	const TABLE = "association_due";
	private $data = array("id" => null, "association_id" => null, "date_added" => null, 
		"removed" => null, "removed_by" => null, "dues" => null, "added_by" => null, 
		'date_removed' => null, 'association' => null);

	public function __set($field, $value){
		if(!array_key_exists($field, $this->data)){
			throw new Exception($field." is not a valid member of ".get_class());
		}
		$this->data[$field] = $value;
	}

	public function __get($field){
		if(!array_key_exists($field, $this->data)){
			throw new Exception($field." is not a valid member of ".get_class());
		}
		return $this->data[$field];
	}

	public function save(){
		$query = "insert into ".AssociationDue::TABLE." (association_id, dues, date_added, added_by) ".
			"value ($this->association_id, $this->dues,'".date("d-m-Y")."',$this->added_by)";
		$conn = Db::get_connection();
		return $conn->exec($query);
	}

	public function update(){
		$query = "update ".AssociationDue::TABLE." set dues = $this->dues where id = $this->id";
		$conn = Db::get_connection();
		return $conn->exec($query);
	}

	public function delete(){
		$query = "update ".AssociationDue::TABLE." set removed = 1, removed_by = $this->removed_by".
			", date_removed = '".date('d-m-Y')."' where id = $this->id";
		$conn = Db::get_connection();
		return $conn->exec($query);
	}

	private static function returnAssociationDueFromResource($resource){
		return returnObjectFromResource(get_class(), $resource, array("id", "association_id",
			"dues", "date_added", "removed_by", "removed", 'date_removed', 'association'));
	}

	public static function toJson($args){
		return ToJson(get_class(), $args, array("id", "association_id", "dues", "date_added", 
			"removed", "removed_by", 'date_removed', 'association'));
	}

	public static function listAssociationDues($args = array("index" => 0, "limit" => 100, 
		"removed" => 0)){
		
		$query = "select a.*, b.name as association from ".AssociationDue::TABLE.
			" as a join ".Association::TABLE." as b on (a.association_id = b.id)";
		
		$args['and'] = false;

		if(array_key_exists("association_id", $args) && $args['association_id'] != 0){
			$query .= " where association_id = ".$args['association_id'];
			$args['and'] = true;
		}

		if($args['and'] == true){
			$query .= " and";
		}else{
			$query .= " where";
		}

		$query .= " a.removed = ".$args['removed']." order by id desc ";
		$query .= check_list_limits($args);

		$conn = Db::get_connection();

		$associationDues = array();
		foreach ($conn->query($query) as $row) {
			array_push($associationDues, 
				AssociationDue::returnAssociationDueFromResource($row));
		}
		return $associationDues;
	}

	public static function findAssociationDue($association_id){
		$conn = Db::get_connection();
		$query = "select a.*, b.name as association from ".AssociationDue::TABLE.
			" as a join ".Association::TABLE." as b on (a.association_id = b.id)".
			" where a.id = $association_id and a.removed = 0 limit 1";
		$result = null;
		foreach ($conn->query($query) as $row) {
			$result = AssociationDue::returnAssociationDueFromResource($row);
		}
		return $result;
	}

	public static function countAssociationDue($removed = 0){
		$query = "select count(id) as row_count from ".AssociationDue::TABLE.
			" where removed = $removed limit 1";
		$conn = Db::get_connection();
		$count = 0;
		foreach ($conn->query($query) as $row) {
			$count = $row['row_count'];
		}
		return $count;
	}

}