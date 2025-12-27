<?php

class RolesModel extends Mysql{

	public $intId;
	public $strRol;
	public $strDescripcion;
	public $intStatus;

	public function __construct()
	{
		parent::__construct();
	}

	public function getRole($columns=[], $condition=NULL){
				
		$query = "SELECT ". (count($columns) ? implode(', ', $columns) : "*") ." 
				  FROM role 
				  ". ($condition ? " WHERE $condition " : '') ." 
				  ORDER BY id DESC";

		$request = $this -> select_all($query);
		
		return $request;
	}

	public function insertRol(string $name, string $description, int $level, int $status)
	{
		$sql = "SELECT * FROM role WHERE name = '{$name}'";
		$request = $this->select_all($sql);

		if(empty($request))
		{
			$sql = "INSERT INTO role (name, description, level, status) VALUES (?,?,?,?) ";
			$request_insert = $this->insert($sql, [$name, $description, $level, $status]);
			$return = $request_insert;
		}else{
			$return = "exist";
		}

		return $return;
	}

	public function updateRol(int $id, string $name, string $description, int $level, int $status)
	{
		$sql = "SELECT * FROM role WHERE name = '$name' AND id != $id";
		$request = $this->select_all($sql);

		if(empty($request))
		{
			$sql = "UPDATE role SET name = ?, description = ?, level = ?, status = ? WHERE id = ?";
			$request = $this->update($sql, [$name, $description, $level, $status, $id]);
		}else{
			$request = "exist";
		}

		return $request;
	}

	public function deleteRol(int $idrol)
	{
		$this->intId = $idrol;
		$sql = "SELECT * FROM user WHERE role_id = $this->intId ";
		$request = $this->select_all($sql);

		if(empty($request))
		{
			$sql = "DELETE FROM role WHERE id = $this->intId ";
			$request = $this->delete($sql);
			if($request)
			{
				$request = 'ok';
			}else{
				$request = 'error';
			}
		}else{
			$request = 'exist';
		}
		return $request;
	}

	public function setLog(int $idUser, string $accion){
		$this->intIdUsuario = $idUser;
		$sql = "INSERT INTO system_logs(user_id, module, action) VALUES (?, ?, ?)";
		$request = $this->update($sql, [$this->intIdUsuario, 'role', $accion]);
		return $request;	
	}
}
?>