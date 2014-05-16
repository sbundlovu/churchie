<?php

/**
*  Member Association Due
*/
class MemberAssociationDue
{
	
	const TABLE = "member_association_due";

	private $data = array("id" => null, "member_id" => null, "association_id" => null, 
		"dues" => null, "month" => null, "year" => null, "date_added" => null, 
		"added_by" => null, "removed" => null, "removed_by" => null, 'date_removed' => null);

	public function __get($field)
	{
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

	public function save(){
		$query = "insert into ".MemberAssociationDue::TABLE." (member_id, association_id, dues, month, year, ".
			"date_added, added_by) values ($this->member_id, $this->association_id, $this->dues, '$this->month', ".
			"'$this->year','".date("d-m-Y")."', $this->added_by)";
		$conn = Db::get_connection();
		return $conn->exec($query);
	}

	public function update(){
		$query = "update ".MemberAssociationDue::TABLE." set association_id = $this->association_id, ".
			"dues = $this->dues, month = $this->month where id = $this->id";
		$conn = Db::get_connection();
		return $conn->exec($query);
	}

	public function delete(){
		$query = "update ".MemberAssociationDue::TABLE." set removed_by = $this->removed_by, removed ".
			"= 1, date_removed = '".date('d-m-Y')."' where id = $this->id";
		$conn = Db::get_connection();
		return $conn->exec($query);
	}
	
	private static function returnMemberAssociationDueFromResource($resource){
		return returnObjectFromResource(get_class(), $resource, array("id", 
			"member_id", "association_id", "dues", "year", "month", "date_added", 
			"added_by", "removed", "removed_by", 'date_removed'));
	}

	public static function toJson($args){
		return ToJson(get_class(), $args, array("id", "member_id", "association_id", 
			"dues", "year", "month", "date_added", "added_by", "removed", "removed_by", 
			'date_removed'));
	}

	public static function listMemberAssociationDues($args = array("member_id" => 0, "index" => 0, 
		"limit" => 100, "removed" => 0, 'month' => null, 'year' => null)){
		$query = "select * from ".MemberAssociationDue::TABLE;

		if(array_key_exists("member_id", $args) && $args["member_id"] != 0){
			$query .= " where member_id = ".$args['member_id'];
			$args['and'] = true;
		}

		if(array_key_exists('month', $args) && $args['month'] != null){
			$query .= " and month = '".$args['month']."'";
			$args['and'] = true;
		}

		if(array_key_exists('year', $args) && $args['year'] != null){
			$query .= " and year = '".$args['year']."'";
			$args['and'] = true;
		}

		if(array_key_exists('association_id', $args) && $args['association_id'] != null){
			$query .= " association_id = ".$args['association_id'];
			$args['and'] = true;
		}
		
		$query = queryBuilder($query, $args);
		$conn = Db::get_connection();
		$memberAssociationDues = array();
		foreach ($conn->query($query) as $row) {
			array_push($memberAssociationDues, MemberAssociationDue::returnMemberAssociationDueFromResource($row));
		}
		return $memberAssociationDues;
	}

	public static function findMemberAssociationDue($id){
		$query = "select * from ".MemberAssociationDue::TABLE." where id = $id limit 1";
		$result = null;
		$conn = Db::get_connection();
		foreach ($conn->query($query) as $row) {
			$result = MemberAssociationDue::returnMemberAssociationDueFromResource($row);
		}
		return $result;
	}

	public static function countMemberAssociationDue($args = array('removed' => 0, 
		'member_id' => null, 'association_id' => null, 'month' => null, 'year' => null)){
		$query = "select count(id) as row_count from ".MemberAssociationDue::TABLE;

		$args['and'] = false;
		
		if(array_key_exists('member_id', $args) && $args['member_id'] != null){
			$query .= (' where member_id ='.$args['member_id']);
			$args['and'] = true;
		}

		if(array_key_exists('association_id', $args) && $args['association_id'] != null){
			if ($args['and'] == true){
				$query .= ' and association_id = '.$args['association_id'];
			}else{
				$query .= ' where association_id = '.$args['association_id'];
			}
			$args['and'] = true;
		}

		if(array_key_exists('month', $args) && $args['month'] != null){
			if ($args['and'] == true){
				$query .= " and month = '".$args['month']."'";
			}else{
				$query .= " where month = '".$args['month']."'";
			}
			$args['and'] = true;
		}
		if(array_key_exists('year', $args) && $args['year'] != null){
			if ($args['and'] == true){
				$query .= " and year = '".$args['year']."'";
			}else{
				$query .= " where year = '".$args['year']."'";
			}
			$args['and'] = true;
		}
		if(array_key_exists('removed', $args) && $args['removed'] != null){
			if ($args['and'] == true){
				$query .= ' and removed = '.$args['removed'];
			}else{
				$query .= ' where removed = '.$args['removed'];
			}
		}
		$query .= " limit 1";
		$count = 0;
		$conn = Db::get_connection();
		print $query;
		foreach ($conn->query($query) as $row) {
			$count = $row['row_count'];
		}
		return $count;
	}

	public static function getFilters($args = array('member_id', 'association_id', 'year', 'month'), 
		$filter = array('removed' => 0)){
		return getFilters(MemberAssociationDue::TABLE, $args, $filter);
	}
}
