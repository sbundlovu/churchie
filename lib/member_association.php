<?php


/**
* Member Association
*/
class MemberAssociation
{
	const TABLE = "member_association";
	private $data = array("id" => null, "member_id" => null, "association_id" => null, 
		"added_by" => null, "date_added" => null, "removed" => null, "removed_by" => null,
		"reason_removed" => null, "date_removed" => null);

	public function __set($field, $value){
		if (!array_key_exists($field, $this->data)) {
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

	public function save()
	{
		$query = "insert into ".MemberAssociation::TABLE." (member_id, association_id,".
			" added_by, date_added) values ($this->member_id, $this->association_id, ".
			"$this->added_by, '".date("d-m-Y")."')";
		$conn = Db::get_connection();
		return $conn->exec($query);
	}

	public function update()
	{
		$query = "update ".MemberAssociation::TABLE." set association_id = $this->association_id".
			" where id = $this->id";
		$conn = Db::get_connection();
		return $conn->exec($query);
	}

	public function delete(){
		$query = "update ".MemberAssociation::TABLE." set removed_by = $this->removed_by, removed = 1".
			", reason_removed = '$this->reason_removed', date_removed = '".date('d-m-Y')."' where id = $this->id";
		$conn = Db::get_connection();
		return $conn->exec($query);
	}

	private static function returnMemberAssociationFromResource($resource){
		return returnObjectFromResource(get_class(), $resource, array("id", "member_id",
			"association_id", "added_by", "date_added", "removed", "removed_by", 
			"reason_removed", "date_removed"));
	}

	public static function toJson($args){
		return ToJson(get_class(), $args, array("id", "member_id", "association_id", 
			"added_by", "date_added", "removed", "removed_by", "reason_removed", 
			"date_removed"));
	}

	public static function listMemberAssociations($args = array("member_id" => null, 
		'association_id' => null, "index" => 0, "limit" => 100, "removed" => 0)){

		$query = "select * from ".MemberAssociation::TABLE;
		if(array_key_exists('member_id', $args) && $args['member_id'] != null){
			$query .= ' where member_id = '.$args['member_id']; 
			$args['and'] = true;
		}
		if(array_key_exists('association_id', $args) && $args['association_id'] != null){
			$query .= ' and association_id = '.$args['association_id'];
			$args['and'] = true;
		}
		$query = queryBuilder($query, $args);
		$conn = Db::get_connection();
		$memberAssociations = array();
		foreach ($conn->query($query) as $row) {
			array_push($memberAssociations, 
				MemberAssociation::returnMemberAssociationFromResource($row));
		}
		return $memberAssociations;
	}

	public static function findMemberAssociation($id){
		$query = "select * from ".MemberAssociation::TABLE." where id = $id limit 1";
		$conn = Db::get_connection();
		$result = null;
		foreach ($conn->query($query) as $row) {
			$result = MemberAssociation::returnMemberAssociationFromResource($row);
		}
		return $result;
	}

	public static function countMemberAssociation($args = array('removed' => 0, 
		'member_id' => null, 'association_id' => null)){
		$query = "select count(id) as row_count from ".MemberAssociation::TABLE.
			" where removed = ".$args['removed'];
		if(array_key_exists('member_id', $args) && $args['member_id'] != null){
			$query .= " and member_id = ".$args['member_id'];
		}
		if(array_key_exists('association_id', $args) && $args['association_id'] != null){
			$query .= " and association_id = ".$args['association_id'];
		}
		$query .=" limit 1";
		$conn = Db::get_connection();
		$count = 0;
		foreach ($conn->query($query) as $row) {
			$count = $row['row_count'];
		}
		return $count;
	}

	public static function getFilters($args = array('member_id', 'association_id'), 
		$filter = array('removed' => 0)){
		return getFilters(MemberAssociation::TABLE, $args, $filter);
	}
}