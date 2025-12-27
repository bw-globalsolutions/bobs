<?php
class LocationModel extends Mysql
{

	public function __construct()
	{
		parent::__construct();
	}
	//locacion
	public function getLocation($columns = [], $condition = NULL)
	{

		$query = "SELECT " . (count($columns) ? implode(', ', $columns) : "*") . " 
				  FROM location 
				  " . ($condition ? " WHERE $condition " : '') . " 
				  ORDER BY id ASC";

		$res = new Mysql;
		$request = $res->select_all($query);

		return $request;
	}

	public function insertLocation($args)
	{

		$query = "INSERT INTO location SET ";
		$values = [];
		foreach ($args as $key => $val) {
			$query .= "`$key` = ?, ";
			$values[] = $val;
		}
		$query = substr($query, 0, -2);

		$res = new Mysql;
		$request = $res->insert($query, $values);

		return $request;
	}

	public function updateLocation($args, $condition = "id = 0")
	{

		$query = "UPDATE location SET ";
		$values = [];
		foreach ($args as $key => $val) {
			$query .= "`$key` = ?, ";
			$values[] = $val;
		}
		$query = substr($query, 0, -2) . " WHERE $condition";
		$res = new Mysql;
		$request = $res->update($query, $values);

		return $request;
	}

	public function deleteLocation($condition = 'id=0')
	{

		$query = "DELETE FROM location WHERE $condition ";

		$res = new Mysql;
		$request = $res->delete($query);

		return $request;
	}

	public function getLocationEmails($type, int $location_id)
	{
		$query = "SELECT GROUP_CONCAT(u.email SEPARATOR ',') AS 'emails' 
		FROM user u 
		INNER JOIN role r ON(r.id=u.role_id) 
		WHERE u.email NOT IN('ljuinio@ppiholdingsinc.com.ph','ljuinio@pizzahut.com.ph') AND u.status=1 AND u.notification=1 AND FIND_IN_SET($location_id, u.location_id) AND r.name IN('" . implode("','", $type) . "')";
		$request = $this->select($query);

		
		return $request['emails'];
	}

	public function getCountryEmails($type, int $country_id)
	{
		$query = "SELECT GROUP_CONCAT(u.email SEPARATOR ',') AS 'emails' FROM user u INNER JOIN role r ON(r.id=u.role_id) WHERE u.status=1 AND u.notification=1 AND FIND_IN_SET($country_id, u.country_id) AND r.name IN('" . implode("','", $type) . "')";
		$request = $this->select($query);
		return $request['emails'];
	}

	public function getFranchiseEmails($type, int $franchise_id)
	{
		$query = "SELECT GROUP_CONCAT(u.email SEPARATOR ',') AS 'emails' FROM user u INNER JOIN role r ON(r.id=u.role_id) WHERE u.status=1 AND u.notification=1 AND FIND_IN_SET($franchise_id, u.franchise_id) AND r.name IN('" . implode("','", $type) . "')";
		$request = $this->select($query);
		return $request['emails'];
	}

	public function getRegionEmails($type, int $country_id)
	{
		$query = "SELECT id FROM country WHERE region = (SELECT region FROM country WHERE id = $country_id)";
		$country_set = array_reduce($this->select_all($query), function ($acc, $item) {
			$acc .= "OR FIND_IN_SET({$item['id']}, u.country_id) ";
			return $acc;
		}, '0 ');

		$query = "SELECT GROUP_CONCAT(u.email SEPARATOR ',') AS 'emails' FROM user u INNER JOIN role r ON(r.id=u.role_id) WHERE u.status=1 AND u.notification=1 AND ($country_set) AND r.name IN('" . implode("','", $type) . "')";
		$request = $this->select($query);
		return $request['emails'];
	}

	public function existsLocation($location_number)
	{
		$numbers = implode("','", $location_number);
		$query_select = "SELECT number FROM location WHERE number IN('$numbers')";
		$tmp = $this->select_all($query_select);
		$request = array_column($tmp, 'number');
		return $request;
	}

	public function setLog(int $idUser, string $accion)
	{
		$this->intIdUsuario = $idUser;
		$sql = "INSERT INTO system_logs(user_id, module, action) VALUES (?, ?, ?)";
		$request = $this->update($sql, [$this->intIdUsuario, 'location', $accion]);
		return $request;
	}
}
