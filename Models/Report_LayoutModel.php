<?php
class Report_LayoutModel extends Mysql {
	
	public function __contruct(){
		parent::__construct();

	}
	
	public function getReport_Layout($columns=[], $condition=NULL){
				
		$query = "SELECT ". (count($columns) ? implode(', ', $columns) : "*") ." 
				  FROM report_layout 
				  ". ($condition ? " WHERE $condition " : '') ." 
				  ORDER BY id DESC";
		
		$res = new Mysql;
		$request = $res -> select_all($query);
		
		return $request;
	}
}
?>