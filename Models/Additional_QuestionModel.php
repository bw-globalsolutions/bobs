<?php
class Additional_QuestionModel extends Mysql {
	
	public function __contruct(){
		parent::__construct();

	}
	//locacion
	public function getAdditional_Question($columns=[], $condition=NULL){
				
		$query = "SELECT ". (count($columns) ? implode(', ', $columns) : "*") ." 
				  FROM additional_question 
				  ". ($condition ? " WHERE $condition " : '') ." 
				  ORDER BY id DESC";


		//echo $query;
		
		$res = new Mysql;
		$request = $res -> select_all($query);
		
		return $request;
	}

	public function listAdditional_Question(int $audit_id, string $lan){		
		$query = "SELECT qi.id, qi.type, qi.input_type, IFNULL(qi.{$lan}, eng) as 'text', qi.eng, IFNULL(qi.{$lan}_answer, eng_answer) as 'qanswer', aq.answer, af.url FROM additional_question_item qi 
			LEFT JOIN (SELECT answer, additional_question_item_id FROM audit_addi_question WHERE audit_id = $audit_id) aq ON(qi.id = aq.additional_question_item_id) 
			LEFT JOIN (SELECT url, reference_id FROM audit_file WHERE audit_id = $audit_id AND (type = 'Additional Questions' OR type ='General Pictures')) af ON(qi.id = af.reference_id) 
			WHERE qi.additional_question_id = (SELECT c.additional_question_id FROM checklist c INNER JOIN audit a ON (c.id = a.checklist_id) WHERE a.id = $audit_id) ORDER BY qi.id ASC";
		
		$request = $this -> select_all($query);
		return $request;
	}
}
?>