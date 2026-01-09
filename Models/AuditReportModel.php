<?php
class AuditReportModel extends Mysql{

	public function __construct()
	{
		parent::__construct();
	}

    public function getSectionsOpp(int $audit_id, int $checklist_id){
        $sql = "SELECT ci.main_section, 
                       ci.section_number, 
                       ci.section_name, 
                       SUM(IF(ap.lost_point > 0, 1, 0)) AS 'questions', 
                       SUM(ci.points) AS 'target', 
                       IFNULL(SUM(ap.lost_point), 0) AS 'points' 
                FROM checklist_item ci 
                LEFT JOIN (SELECT lost_point, question_prefix FROM audit_point WHERE audit_id = $audit_id) ap ON (ci.question_prefix = ap.question_prefix) WHERE ci.checklist_id = $checklist_id AND ci.type = 'Question' AND ci.section_name != 'Information' GROUP BY ci.main_section, ci.section_number, ci.section_name ORDER BY section_number ASC";
        $request = [];
        foreach($this->select_all($sql) as $s){
            if(empty($request[$s['main_section']])){
                $request[$s['main_section']] = [];
            }
            array_push($request[$s['main_section']], [
                'section_number'    => $s['section_number'], 
                'section_name'      => $s['section_name'],
                'questions'         => $s['questions'],
                'target'            => $s['target'],
                'points'            => (float)$s['points']
            ]);
        }
		return $request;
	}

    public function getQuestionsOpp(int $audit_id, int $checklist_id, $lan){
        if($lan==NULL)$lan='eng';
        $sql = "SELECT ci.section_name, IFNULL(ci.$lan, ci.eng) AS 'txt', ci.question_prefix, ci.priority FROM checklist_item ci INNER JOIN audit_point ap ON (ci.question_prefix = ap.question_prefix) WHERE ci.type = 'Question' AND ap.audit_id = $audit_id AND ci.checklist_id = $checklist_id AND ci.section_name!='Information'";
        $request = [];

        foreach($this->select_all($sql) as $q){
            $sql = "SELECT ci.id, ci.priority, IFNULL(ci.$lan, ci.eng) AS 'txt', (SELECT GROUP_CONCAT(url SEPARATOR '|') FROM audit_file WHERE type = 'Opportunity' AND reference_id = ao.id) as 'stack_img', ao.auditor_answer, ao.auditor_comment FROM checklist_item ci INNER JOIN audit_opp ao ON(ci.id = ao.checklist_item_id AND ci.checklist_id = $checklist_id) WHERE ci.checklist_id = $checklist_id AND ao.audit_id = $audit_id AND ci.type = 'Picklist' AND ci.question_prefix = '{$q['question_prefix']}'";
            $picklist = [];

            
            foreach($this->select_all($sql) as $p){
                $answers = [];
                foreach(listAnswers($lan, $p['id']) as $key => $value){
                    if(matchAnswer($key, $p['auditor_answer'])) array_push($answers, $value);
                }
                
                array_push($picklist, [
                    'text'      => $p['txt'],
                    'answers'   => $answers,
                    'comment'   => $p['auditor_comment'],
                    'priority'  => $p['priority'],
                    'stack_img' => empty($p['stack_img'])? [] : explode('|', $p['stack_img'])
                ]);
            }

            if(empty($request[$q['section_name']])){
                $request[$q['section_name']] = [];
            }
            array_push($request[$q['section_name']], [
                'priority'  => $q['priority'],
                'question'  => $q['txt'],
                'prefix'    => $q['question_prefix'],
                'picklist'  => $picklist
            ]);
        }
		return $request;
    }

    public function getPreviousAudit(string $lnumber, string $type, string $dvisit){
        $sql = "SELECT id, scoring_id FROM audit_list WHERE location_number = $lnumber AND type IN('$type') AND date_visit < '$dvisit' AND status = 'Completed' ORDER BY date_visit DESC";
        $response = $this->select($sql);
        return $response;
    }

    public function countTotalOpps(int $audit_id){
        $sql = "SELECT count(1) AS opps FROM audit_opp WHERE audit_id = $audit_id";
        $response = $this->select($sql);
        return $response;
    }

    public function getAuditListById($audit_id){	
		$query = "SELECT checklist_id, 
                         type, 
                         round_name, 
                         status, 
                         date_visit, 
                         date_visit_end, 
                         location_id, 
                         location_number, 
                         location_name, 
                         location_address, 
                         auditor_name, 
                         manager_name, 
                         manager_signature, 
                         scoring_id,
                         country_id,
                         id,
                         (SELECT url FROM audit_file b WHERE b.audit_id = id AND (name = 'Picture of the Front Door/Entrance of the Restaurant' OR name = 'Foto de entrada principal del restaurante') LIMIT 1) picture_front
                    FROM audit_list a 
                    WHERE id = $audit_id ORDER BY date_visit DESC, id DESC";
		$request = $this->select($query);
		return $request;
	}

    public function getPointsPerSection(int $audit_id, int $checklist_id){
        $sql = "SELECT SUM(ci.points) AS 'points', ap.lost_point, CEIL(100 - IFNULL(ap.lost_point/ SUM(ci.points) * 100, 0)) AS 'avg', IFNULL(ap.critics, 0) AS 'critics', ci.main_section, ci.section_number, ci.section_name FROM checklist_item ci LEFT JOIN (SELECT ap.section_number, IFNULL(SUM(ap.lost_point), 0) AS 'lost_point', SUM(IF((SELECT priority='Critical' FROM checklist_item WHERE id = ao.checklist_item_id AND checklist_id = $checklist_id), 1, 0)) AS 'critics' FROM audit_point ap INNER JOIN audit_opp ao ON(ap.id=ao.audit_point_id) WHERE ap.audit_id = $audit_id GROUP BY ap.section_number)ap ON(ap.section_number=ci.section_number) WHERE ci.checklist_id = $checklist_id AND ci.type = 'Question' AND ci.question_prefix NOT IN(SELECT question_prefix FROM audit_na_question WHERE audit_id = $audit_id) AND (SELECT audited_areas IS NULL OR FIND_IN_SET(ci.area, REPLACE(audited_areas, '|', ',')) FROM audit WHERE id = $audit_id) GROUP BY ci.main_section, ci.section_number, ci.section_name, ap.lost_point, ap.critics";

        $request = [];
        foreach($this->select_all($sql) as $s){
            $request[$s['main_section']][$s['section_number']] = [
                'section_name'      => $s['section_name'],
                'avg'               => $s['avg'],
                'critics'           => $s['critics'],
                'tp'                => $s['points'],
                'ep'                => (float) $s['lost_point']
            ];
        }
        return array_reverse($request);
    }

    public function getHistorical(int $audit_id, string $type, string $date_visit, int $location_id, int $checklist_id, string $lan){
        $sql = "SELECT a.id, DATE_FORMAT(a.date_visit, '%d/%m/%Y') AS 'date_visit' FROM audit a INNER JOIN round r ON (a.round_id = r.id) WHERE r.type = '$type' AND a.location_id = '$location_id' AND (a.id = '$audit_id' OR (a.date_visit < '$date_visit' AND a.status = 'Completed')) ORDER BY a.date_visit DESC LIMIT 4";
        
        $subQuerys = array_reduce($this->select_all($sql), function($acc, $cur){ return "$acc, EXISTS(SELECT * FROM audit_point WHERE audit_id = {$cur['id']} AND question_prefix = ci.question_prefix) AS '{$cur['date_visit']}'"; }, "");

        $sql = "SELECT section_name, CONCAT(question_prefix, ' - ', $lan) AS 'QUESTION' $subQuerys FROM checklist_item ci WHERE type = 'Question' AND checklist_id = $checklist_id AND question_prefix NOT IN(SELECT question_prefix FROM audit_na_question WHERE audit_id = $audit_id) AND (SELECT audited_areas IS NULL OR FIND_IN_SET(ci.area, REPLACE(audited_areas, '|', ',')) FROM audit WHERE id = $audit_id) ORDER BY section_number";
        $request = $this->select_all($sql);

        return $request;
    }

    public function haveAutoFail(int $audit_id){
		$sql = "SELECT * FROM audit_opp ao INNER JOIN checklist_item ci ON (ao.checklist_item_id = ci.id) WHERE ao.audit_id = $audit_id AND ci.auto_fail IS NOT NULL";
        $request = $this->select_all($sql);

        return !empty($request);
	}
}
?>