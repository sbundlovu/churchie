<?php

/**
* Menu Item
*/
class MenuItem
{
	const TABLE = "menu_structure";
	private $data = array('id' => null, 'name' => null, 'endpoint' => null, 'usertype' => null);

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

	private static function getMenuItemFromResource($resource){
		$tmp = new MenuItem();
		$tmp->id = $resource['id'];
		$tmp->name = $resource['menu_name'];
		$tmp->endpoint = $resource['endpoint'];
		$tmp->usertype = $resource['usertype'];
		return $tmp;
	}

	public static function listMenu($usertype){
		$query = "select * from ".MenuItem::TABLE.
			" where usertype = '$usertype' order by usertype, menu_name";
		$conn = Db::get_connection();
		$menuItems = array();
		foreach ($conn->query($query) as $row) {
			array_push($menuItems, MenuItem::getMenuItemFromResource($row));
		}
		return $menuItems;
	}

	public static function IsUserAuthourized($usertype, $endpoint){
		$query = "select count(id) from ".MenuItem::TABLE.
			" where usertype = $usertype and endpoint = $endpoint";
		$conn = Db::get_connection();
		$count = 0;
		foreach ($conn->query($query) as $row) {
			$count = $row['id'];
		}
		return ($count > 0);
	}

	public static function toJson($args){
		return ToJson(get_class(), $args, array("id", "name", "endpoint", "usertype"));
	}
}