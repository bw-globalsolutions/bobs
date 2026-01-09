<?php
class Checklist_ItemModel extends Mysql {
	
	public function __contruct(){
		parent::__construct();

	}

	//Items del checklist
	public function getChecklistItem($columns=[], $condition=NULL){
				
		$query = "SELECT ". (count($columns) ? implode(', ', $columns) : "*") ." 
				  FROM checklist_item   
				  ". ($condition ? " WHERE $condition " : '') ." 
				  ORDER BY id ASC";
		
		$res = new Mysql;
		$request = $res -> select_all($query);
		
		return $request;
	}

	public function getChecklistSection($condition=NULL){
				
		$query = "SELECT 
					main_section,
					section_number,
					section_name, 
					COUNT(1) AS 'tot_questions',
					SUM(points) AS 'tot_points'
				  FROM checklist_item   
				  	". ($condition ? "WHERE $condition " : NULL) . "
				  GROUP BY main_section, section_number, section_name 
				  ORDER BY section_number ASC";		

		$res = new Mysql;
		$request = $res -> select_all($query);
		
		return $request;
	}

	public function quitarGuiones($idChecklist){
		$sql = "SELECT id, question_prefix FROM checklist_item WHERE checklist_id =$idChecklist";
		$res = new Mysql;
		$request = $res -> select_all($sql);

		foreach($request as $r){
			if(strpos($r['question_prefix'], '_') !== false){
				$qp = explode('_', $r['question_prefix'])[0];
				$sql = "UPDATE checklist_item SET question_prefix = '".$qp."' WHERE id =".$r['id'];
				$rs = $res->update($sql, []);
			}
		}
	}

	public function crearPor($idChecklist){
		$sql = "SELECT id, eng, eng_answer FROM checklist_item WHERE checklist_id =$idChecklist";
		$res = new Mysql;
		$request = $res -> select_all($sql);

		foreach($request as $r){
			$sql = "UPDATE checklist_item SET por = '".$r['eng']."', por_answer = '".$r['eng_answer']."' WHERE id =".$r['id'];
			$rs = $res->update($sql, []);
		}
	}
}
?>