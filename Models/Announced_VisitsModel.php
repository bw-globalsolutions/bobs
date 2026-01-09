<?php
class Announced_VisitsModel extends Mysql {
	
	public function __construct(){
		parent::__construct();
	}
	    	
	public function getChecklist(int $checklist_id, int $audit_id, string $col){
        $sql = "SELECT id, $col, priority, points, question_prefix, section_number FROM checklist_item WHERE type = 'Question' AND checklist_id = $checklist_id";
		$request = [];
		foreach($this -> select_all($sql) as $q){
			$sql = "SELECT id, $col AS 'picklist_item', IF(EXISTS(SELECT * FROM audit_opp WHERE audit_id = $audit_id AND checklist_item_id = ci.id), 1, 0) AS 'has_opp' FROM checklist_item ci WHERE type = 'Picklist' AND question_prefix = '{$q['question_prefix']}' AND checklist_id = $checklist_id";
			array_push($request, [
				'question'		=> $q[$col],
				'prefix'		=> $q['question_prefix'],
				'snumber'		=> $q['section_number'],
				'priority'		=> $q['priority'],
				'points'		=> $q['points'],
				'picklist'		=> $this -> select_all($sql)
			]);
		}
		return $request;
	}
	
	public function getTypes(){
        $sql = "SELECT DISTINCT type FROM round";
        $request = $this -> select_all($sql);
		return $request;
	}
	
	public function getAuditLanguage(int $id){
        $sql = "SELECT c.language FROM audit a INNER JOIN location l ON (a.location_id = l.id) INNER JOIN country c ON (l.country_id = c.id) WHERE a.id = $id";
        $request = $this -> select($sql);
		return $request['language'];
	}
    
	public function getAuditList($columns=[], $condition=NULL){	
		$query = "SELECT ". (count($columns) ? implode(',', $columns) : "*") ." 
				  FROM audit_list 
				  ". ($condition ? " WHERE $condition " : '') ." 
				  ORDER BY id DESC";
		
		$request = $this -> select_all($query);
		return $request;
	}
	
	public function getAudits($columns=[], $condition=NULL){	
		$query = "SELECT ". (count($columns) ? implode(',', $columns) : "*") ." 
				  FROM audit 
				  ". ($condition ? " WHERE $condition " : '') ." 
				  ORDER BY id ASC";
		
		$request = $this -> select_all($query);
		return $request;
	}

	public function updateAudit($args, $condition = "id = 0"){
		
		$query = "UPDATE audit SET ";
		$values = [];
		foreach($args as $key => $val){
			$query .= "`$key` = ?, ";
			$values[] = $val;
		}
		$query = substr($query, 0, -2) ." WHERE $condition";
		$request = $this->update($query, $values);
		
		return $request;
	}

	public function getAuditorNameList(int $week){

		//echo $week;
		
		$query = "SELECT 
					t1.id, t1.auditor_name, t1.auditor_email
				FROM audit t1
				inner join (SELECT 
								t4.*,
								t5.name country_name,
								t5.prefix country_prefix,
								t5.region country_region
							FROM round t4
							inner join country t5
							on t4.country_id = t5.id
							where type = 'Standard') t2
				on t1.round_id = t2.id
				inner join location t3
				on t1.location_id = t3.id
				where t1.status = 'Pending'
				and WEEKOFYEAR(t1.announced_date) = ".$week." 
				group by t1.id, t1.auditor_email 
				order by t1.id";
		
		//echo $query;
		
		$res = new Mysql;
		$request = $res -> select_all($query);
		
		return $request;
	}

	public function getCountryNameList(int $week){

		//echo $week;
		
		$query = "SELECT
					t2.country_name
				FROM audit t1
				inner join (SELECT 
								t4.*,
								t5.name country_name,
								t5.prefix country_prefix,
								t5.region country_region
							FROM round t4
							inner join country t5
							on t4.country_id = t5.id
							where type = 'Standard') t2
				on t1.round_id = t2.id
				inner join location t3
				on t1.location_id = t3.id
				where t1.status = 'Pending'
				and WEEKOFYEAR(t1.announced_date) = ".$week." 
				group by t2.country_name";
		
		//echo $query;
		
		$res = new Mysql;
		$request = $res -> select_all($query);
		
		return $request;
	}

	public function getAnnouncedVisitList($condition){

		//echo $week;
		
		$query = "SELECT 
					al.*, al.id as id_visit, 
					WEEKOFYEAR(al.announced_date) select_week,
					lo.*, lo.id as location_id 
				FROM audit_list al
				left join location lo
				on al.location_id = lo.id
				where al.status = 'Pending'
				".$condition." AND type = 'Training-visits' 
				order by al.id";
		
		$res = new Mysql;
		$request = $res -> select_all($query);
		
		return $request;
	}

	public function insertDataLog($args) {
		$query = "INSERT INTO data_logs SET ";
		$values = [];
		foreach($args as $key => $val){
			$query .= "`$key` = ?, ";
			$values[] = $val;
		}
		$query = substr($query, 0, -2);
		
		$res = new Mysql;
		$request = $res -> insert($query, $values);
		
		return $request;
	}

	public function updateDataLog($args, $condition = "id_data_log = 0") {
		
		$query = "UPDATE data_logs SET ";
		$values = [];
		foreach($args as $key => $val){
			$query .= "`$key` = ?, ";
			$values[] = $val;
		}
		$query = substr($query, 0, -2) ." WHERE $condition";
		$res = new Mysql;
		$request = $res -> update($query, $values);
		
		return $request;
	}

	public function setData(string $strData, string $strAccionData){
		
		$query_insert = "INSERT INTO data_logs (data, date_data) 
						VALUES (?,?) ";
		$arrData = array($strData, $strAccionData);
		$res = new Mysql;
		$request = $res ->insert($query_insert,$arrData);
		return $request;
	}

	public function getData(int $intId)
	{
		$sql = "SELECT * FROM data_logs WHERE id_data_log = $intId";
		$res = new Mysql;
		$request = $res->select($sql);
		return $request;
	}

	public function setUnit(int $brand_id, int $country_id, string $status ,string $number ,string $name ,string $address_1 ,string $city ,string $state_code ,string $zip ,string $country ,string $longitude ,string $latitude ,string $phone ,string $email ,string $sun_open ,string $sun_close ,string $mon_open ,string $mon_close ,string $tue_open ,string $tue_close ,string $wed_open ,string $wed_close ,string $thu_open ,string $thu_close ,string $fri_open ,string $fri_close ,string $sat_open ,string $sat_close ,string $qsc_exceptions ,string $master ,string $entity_name){
		
		$query_insert = "INSERT INTO loaction_test (`brand_id`, `country_id`, `status`, `number`, `name`, `address_1`, `city`, `state_code`, `zip`, `country`, `longitude`, `latitude`, `phone`, `email`, `sun_open`, `sun_close`, `mon_open`, `mon_close`, `tue_open`, `tue_close`, `wed_open`, `wed_close`, `thu_open`, `thu_close`, `fri_open`, `fri_close`, `sat_open`, `sat_close`, `qsc_exceptions`, `master`, `entity_name`)
						VALUES (?,?) ";
		echo $query_insert; die();
		$arrData = array($brand_id, $country_id ,$status ,$number ,$name ,$address_1 ,$city ,$state_code ,$zip ,$country ,$longitude ,$latitude ,$phone ,$email ,$sun_open ,$sun_close ,$mon_open ,$mon_close ,$tue_open ,$tue_close ,$wed_open ,$wed_close ,$thu_open ,$thu_close ,$fri_open ,$fri_close ,$sat_open ,$sat_close ,$qsc_exceptions ,$master ,$entity_name );
		$res = new Mysql;
		$request = $res ->insert($query_insert,$arrData);
		return $request;
	}

	public function insertUnitData($args){

		$query = "INSERT INTO location_test SET ";
		$values = [];
		foreach($args as $key => $val){
			$query .= "`$key` = ?, ";
			$values[] = $val;
		}
		$query = substr($query, 0, -2);
		
		$res = new Mysql;
		$request = $res -> insert($query, $values);
		
		return $request;
	}

	public function getFranchises($columns=[], $condition=NULL){	
		$query = "SELECT ". (count($columns) ? implode(',', $columns) : "*") ." 
				  FROM location
				  ". ($condition ? " WHERE $condition " : '') ." 
				  ORDER BY id DESC";
		
		$request = $this -> select_all($query);
		return $request;
	}

}
?>