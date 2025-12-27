<?php

class FilesModel extends Mysql{

	public function __construct()
	{
		parent::__construct();
	}

    public function addFile($title, $description, $jfiles, $idUser){
        $query_insert = "INSERT INTO home_files(title, description, jfiles, user_id) VALUES (?,?,?,?)";
        $arrData = [$title, $description, $jfiles, $idUser];
        $request = $this->insert($query_insert, $arrData);
        return $request;
    }
    
    public function updFile($title, $description, $jfiles, $id){
        $query_update = "UPDATE home_files SET title = ?, description = ?, jfiles = ? WHERE id = ?";
        $arrData = [$title, $description, $jfiles, $id];
        $request = $this->update($query_update, $arrData);
        return $request;
    }
    
    public function getFiles(){
        $query_select = "SELECT hf.id, hf.title, hf.description, hf.jfiles, hf.created, u.name FROM home_files hf INNER JOIN user u ON hf.user_id = u.id WHERE hf.active = 1 ORDER BY hf.id DESC";
        $tmp = $this->select_all($query_select);
        $request = array_map(function($item){
            $item['jfiles'] = json_decode($item['jfiles'], true);
            return $item;
        }, $tmp);
        return $request;
    }

    public function removeFile($id){
        $query_update = "UPDATE home_files SET active = '0' WHERE id = ?";
        $request = $this->update($query_update, [$id]);
        return $request;
    }
}
?>