<?php
class AuditorSurveyModel extends Mysql {
	
	public function __construct(){
		parent::__construct();
	}

	public function getQuestions(){
        $sql = "SELECT * FROM survey_auditor";
        $request = $this->select_all($sql);
		return $request;

        // foreach($this->select_all($sql) as $q){
        //     $sql = "SELECT ci.id, $lan, (SELECT GROUP_CONCAT(url SEPARATOR '|') FROM audit_file WHERE type = 'Opportunity' AND reference_id = ao.id) as 'stack_img', ao.auditor_answer, ao.auditor_comment FROM checklist_item ci INNER JOIN audit_opp ao ON(ci.id = ao.checklist_item_id) WHERE ao.audit_id = $audit_id AND ci.type = 'Picklist' AND ci.question_prefix = '{$q['question_prefix']}'";
        //     $picklist = [];

            
        //     foreach($this->select_all($sql) as $p){
        //         $answers = [];
        //         foreach(listAnswers($lan, $p['id']) as $key => $value){
        //             if(matchAnswer($key, $p['auditor_answer'])) array_push($answers, $value);
        //         }
                
        //         array_push($picklist, [
        //             'text'      => $p[$lan],
        //             'answers'   => $answers,
        //             'comment'   => $p['auditor_comment'],
        //             'stack_img' => empty($p['stack_img'])? [] : explode('|', $p['stack_img'])
        //         ]);
        //     }

        //     if(empty($request[$q['section_name']])){
        //         $request[$q['section_name']] = [];
        //     }
        //     array_push($request[$q['section_name']], [
        //         'priority'  => $q['priority'],
        //         'question'  => $q[$lan],
        //         'prefix'    => $q['question_prefix'],
        //         'picklist'  => $picklist
        //     ]);
        // }
		return $request;
    }

	public function getAuditSurvey($id){
        $sql = "SELECT * FROM audit_survey where audit_id=$id";
		//echo $sql;
        $request = $this->select_all($sql);
		return $request;
    }
	    	
	public function insertAnswer($args){

		//query y values de argumentos
		$query = "INSERT INTO audit_survey SET ";
		$values = [];
		foreach($args as $key => $val){
			$query .= "`$key` = ?, ";
			$values[] = $val;
		}
		$query = substr($query, 0, -2);
		//var_dump($args);
		//echo $query;
		
		//var_dump($args);
		$res = new Mysql;
		$request = $res -> insert($query, $values);
		
		return $request;
	}


    public function insertAnswerText($data){
        
        
       
        $audit_id = $data['audit_id'];
        $question_id =  $data['question_id'];
        $answer = $data['answer'];
        $user_id = $_SESSION['userData']['id'];



        

		$query_insert = "INSERT INTO audit_survey (audit_id,
                                                   question_id,
                                                   answer,
                                                   user_id)
						VALUES (?,?,?,?) ";

		$arrData = array($audit_id,$question_id,$answer,$user_id);
		$request = $this->insert($query_insert,$arrData);
		return $request;
	}


	

}
?>