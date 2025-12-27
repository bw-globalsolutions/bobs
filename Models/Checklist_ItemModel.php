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

	public function getChecklistItemArea($columns=[], $condition=NULL){
				
		$query = "SELECT ". (count($columns) ? implode(', ', $columns) : "*") ." 
				  FROM checklist_item   
				  ". ($condition ? " WHERE $condition " : '') ." ";
		
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
				  
				//  echo $query ;
				

		$res = new Mysql;
		$request = $res -> select_all($query);
		
		return $request;
	}
}
?>