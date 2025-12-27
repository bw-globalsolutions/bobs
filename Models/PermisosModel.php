<?php

class PermisosModel extends Mysql{

	public $intIdpermiso;
	public $intRolid;
	public $intModuloid;
	public $r;
	public $w;
	public $u;
	public $d;

	public function __construct()
	{
		parent::__construct();
	}

	public function getPermissions($columns=[], $condition=NULL){
				
		$query = "SELECT ". (count($columns) ? implode(', ', $columns) : "*") ." 
				  FROM permission 
				  ". ($condition ? " WHERE $condition " : '') ." 
				  ORDER BY id ASC";
		
		$request = $this -> select_all($query);
		
		return $request;
	}

	public function deletePermisos(int $idrol)
	{
		$this->intRolid = $idrol;
		$sql = "DELETE FROM permission WHERE role_id = $this->intRolid ";
		$request = $this->delete($sql);
		return $request;
	}

	public function insertPermisos(int $idrol, int $idmodulo, int $r, int $w, int $u, int $d)
	{
		$this->intRolid = $idrol;
		$this->intModuloid = $idmodulo;
		$this->r = $r;
		$this->w = $w;
		$this->u = $u;
		$this->d = $d;

		$query_insert = "INSERT INTO permission (role_id, module_id, r, w, u, d) VALUES (?,?,?,?,?,?) ";
		$arrData = array($this->intRolid, $this->intModuloid, $this->r, $this->w, $this->u, $this->d);
		$request_insert = $this->insert($query_insert,$arrData);
		return $request_insert;
	}

	public function permisosModulo(int $idrol)
	{
		$this->intRolid = $idrol;
		$sql = "SELECT p.role_id, 
						p.module_id, 
						m.name as modulo, 
						p.r, 
						p.w, 
						p.u, 
						p.d 
				FROM permission p INNER JOIN module m ON p.module_id = m.id 
				WHERE p.role_id = $this->intRolid ";
		$request = $this->select_all($sql);
		$arrPermisos = array();
		for($i=0; $i < count($request); $i++)
		{
			$arrPermisos[$request[$i]['id_modulo']] = $request[$i];
		}
		return $arrPermisos;
	}

	public function getModule($columns=[], $condition=NULL){

		$query = "SELECT ". (count($columns) ? implode(', ', $columns) : "*") ." 
				  FROM module 
				  ". ($condition ? " WHERE $condition " : '') ." 
				  ORDER BY id ASC";
		
		$request = $this->select_all($query);
		
		return $request;
	}

	public function setLog(int $idUser, string $accion){
		$this->intIdUsuario = $idUser;
		$sql = "INSERT INTO system_logs(user_id, module, action) VALUES (?, ?, ?)";
		$request = $this->update($sql, [$this->intIdUsuario, 'permission', $accion]);
		return $request;	
	}
}
?>